<?php

/**
 * @file
 * Contains Phagrancy\ApiTest
 */

namespace Phagrancy;

use Phagrancy\Http\Response;
use Phagrancy\TestCase\Integration;

class ApiTest
	extends Integration
{

	public function provideNotFoundRoutes()
	{
		return [
			// scope index
			['GET', 'nope'],
			['GET', 'te'],
			['GET', 'tes_t'],

			// box definition
			['GET', 'te_st/nope'],

			// box download
			['GET', 'te_st/nope/version/2.0.0/provider/test']
		];
	}

	public function provideGoodRoutes()
	{
		return [
			['GET', 'test', Response\Api\BoxList::class, ['username' => 'test', 'boxes' => ['test']]],
			['GET', 'test/nope', Response\Api\BoxDefinition::class, ['tag' => 'test/nope', 'versions' => []]],
			[
				'GET',
				'test/test',
				Response\Api\BoxDefinition::class,
				[
					'tag'      => 'test/test',
					'versions' => [
						[
							'version'   => '200',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/api/v1/box/test/test/version/200/provider/test'
								]
							],
						],
						[
							'version'   => '2.0.0',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/api/v1/box/test/test/version/2.0.0/provider/test'
								]
							]
						]

					]
				]
			],
			// provider delete
			[
				'DELETE',
				'test/something/version/2.0.0/provider/test',
				Response\Json::class,
				null
			],
			// version release
			[
				'PUT',
				'test/something/version/2.0.0/release',
				Response\Json::class,
				null
			],
			// provider preflight
			[
				'GET',
				'test/something/version/2.0.0/provider/test/upload',
				Response\Json::class,
				[
					'upload_path' => 'http://localhost/api/v1/box/test/something/version/2.0.0/provider/test/upload'
				]
			]
		];
	}

	/**
	 * @dataProvider provideGoodRoutes
	 */
	public function testRoutes($method, $path, $responseClass, $expected)
	{
		$response = $this->runApp($method, "/api/v1/box/{$path}");

		self::assertInstanceOf($responseClass, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageBodyEqualsJsonArray($response, $expected);
	}

	/**
	 * @dataProvider provideNotFoundRoutes
	 */
	public function testRoutesReturnNotFound($method, $path)
	{
		$response = $this->runApp($method, "/api/v1/box/{$path}");

		self::assertInstanceOf(Response\NotFound::class, $response);
		self::assertResponseHasStatus($response, 404);
	}

	public function testDownloadReturnsFile()
	{
		$response = $this->runApp('GET', '/api/v1/box/test/test/version/2.0.0/provider/test');

		self::assertInstanceOf(Response\SendBoxFile::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageHasHeader($response, 'Content-Type', 'application/octet-stream');
		self::assertMessageHasHeader($response, 'Content-Disposition', 'attachment; filename="test-test-2.0.0.box"');

		$b = $response->getBody();
		$b->rewind();
		self::assertEquals('test', $b->getContents());
	}

	public function testCreateVersionReturnsBadRequestWithMissingData()
	{
		$response = $this->runApp('POST', '/api/v1/box/test/something/versions');

		self::assertInstanceOf(Response\InvalidRequest::class, $response);
	}

	public function testCreateVersionReturnsSuccess()
	{
		$body     = json_encode(['version' => ['version' => 2, 'description' => 'something']]);
		$response = $this->runApp('POST', '/api/v1/box/test/something/versions', $body, true);

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, ['version' => 2, 'description' => 'something']);

//		self::assertResponseJsonEqualsString($response, 'version');
	}

	// provider create
	public function testCreateProviderReturnsBadRequest()
	{
		$response = $this->runApp('POST', '/api/v1/box/test/something/version/1.0.0/providers');

		self::assertInstanceOf(Response\InvalidRequest::class, $response);
	}

	public function testCreateProviderReturnsSuccess()
	{
		$body     = json_encode(['provider' => ['name' => 'virtualtest']]);
		$response = $this->runApp('POST', '/api/v1/box/test/something/version/1.0.0/providers', $body, true);

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, [
			'name' => 'virtualtest',
			'upload_url' => 'http://localhost/api/v1/box/test/something/version/1.0.0/provider/virtualtest/upload'
		]);
	}

	// provider upload
	public function testUploadReturnsSuccess()
	{
		\MockPhpStream::register();

		file_put_contents('php://input', 'upload-data');
		$response = $this->runApp(
			'PUT',
			'/api/v1/box/test/something/version/1.0.0/provider/virtualtest/upload'
			);

		\MockPhpStream::restore();

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, []);
		self::assertEquals('upload-data', file_get_contents($this->fs->url() . '/data/storage/test/something/1.0.0/virtualtest.box'));

		$response->getBody()->close();
	}

	public function testUploadReturnsNotFound()
	{
		$response = $this->runApp(
			'PUT',
			'/api/v1/box/tes_t/something/version/1.0.0/provider/virtualtest/upload'
		);

		self::assertInstanceOf(Response\NotFound::class, $response);
	}

	public function testAccessTokenIsRequired()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\napi_token=testing");

		unset($this->app);

		$response = $this->runApp('GET', '/api/v1/box/test');

		self::assertInstanceOf(Response\NotAuthorized::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}

	public function testAccessTokenValidates()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\napi_token=testing");

		unset($this->app);

		$response = $this->runApp('GET', '/api/v1/box/test?access_token=testing');

		self::assertInstanceOf(Response\Api\BoxList::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}
}