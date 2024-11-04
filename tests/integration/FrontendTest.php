<?php

/**
 * @file
 * Contains Phagrancy\Integration\FrontendTest
 */

namespace Phagrancy\Integration;

use Phagrancy\Http\Response;
use Phagrancy\TestCase\Integration;

class FrontendTest
	extends Integration
{
	public function provideNotFoundRoutes()
	{
		return [
			// scope doesn't exist
			['GET', '/nope'],
			// send file on a non-existent box
			['GET', '/test/nope/2.0/test.box'],
			['GET', '/t_est/nope/2.0/test.box'],
			// scopes can't be smaller than 3 characters
			['GET', '/te'],
			// scopes a-z0-9-
			['GET', '/tes_t'],
			// boxes a-z0-9-
			['GET', '/test/tes_t']
		];
	}

	public function provideGoodRoutes()
	{
		return [
			['GET', '', Response\Json::class, null],
			['GET', '/', Response\Json::class, null],
			['GET', '/scopes', Response\ScopeList::class, ['alt', 'arch', 'delete', 'test']],
			['GET', '/test', Response\BoxList::class, ['username' => 'test', 'boxes' => ['test']]],
			['GET', '/test/nope', Response\BoxDefinition::class, ['name' => 'test/nope', 'versions' => []]],
			[
				'GET',
				'/test/test',
				Response\BoxDefinition::class,
				[
					'name'     => 'test/test',
					'versions' => [
						[
							'version'   => '2.0.0',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/test/test/2.0.0/test'
								]
							]
						],
						[
							'version'   => '200',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/test/test/200/test'
								]
							]
						]
					]
				],
			],
		];
	}

	/**
	 * @dataProvider provideGoodRoutes
	 */
	public function testRoutes($method, $path, $responseClass, $expected)
	{
		$response = $this->runApp($method, $path);

		if ($path === '/sacopes') {
			$b = $response->getBody();
			$b->rewind();

			var_dump($b->getContents());
			die();
		}
		self::assertInstanceOf($responseClass, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageBodyEqualsJsonArray($response, $expected);
	}

	/**
	 * @dataProvider provideNotFoundRoutes
	 */
	public function testRoutesReturnNotFound($method, $path)
	{
		$response = $this->runApp($method, $path);

		self::assertInstanceOf(Response\NotFound::class, $response);
		self::assertResponseHasStatus($response, 404);
	}

	public function testDownloadReturnsFile()
	{
		$response = $this->runApp('GET', '/test/test/200/test');

		self::assertInstanceOf(Response\SendBoxFile::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageHasHeader($response, 'Content-Type', 'application/octet-stream');
		self::assertMessageHasHeader($response, 'Content-Disposition', 'attachment; filename="test-test-200.box"');

		$b = $response->getBody();
		$b->rewind();
		self::assertEquals('test', $b->getContents());
	}

	public function testPasswordIsRequiredWhenSet()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\naccess_password=testing");

		unset($this->app);

		$response = $this->runApp('GET', '/test/test');

		self::assertInstanceOf(Response\NotAuthorized::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}

	public function testPasswordValidatesWhenSet()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\naccess_password=testing");

		unset($this->app);

		$this->env = [
			'PHP_AUTH_USER'  => 'someone',
			'PHP_AUTH_PW'    => 'testing'
		] ;
		$response = $this->runApp('GET', '/test');

		self::assertInstanceOf(Response\BoxList::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}
}
