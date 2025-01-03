<?php

/**
 * @file
 * Contains Phagrancy\Integration\ApiTest
 */

namespace Phagrancy\Integration;

use Phagrancy\Http\Response;
use Phagrancy\TestCase\Integration;

class ApiTest
	extends Integration
{
	public function provideNotFoundRoutes(): array
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

	public function provideGoodRoutes(): array
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
							'version'   => '2.0.0',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/api/v1/box/test/test/version/2.0.0/provider/test'
								]
							]
						],
						[
							'version'   => '200',
							'providers' => [
								[
									'name' => 'test',
									'url'  => 'http://localhost/api/v1/box/test/test/version/200/provider/test'
								]
							],
						]
					]
				]
			],
			// provider delete
			[
				'DELETE',
				'delete/test/version/1.0.0/provider/test',
				Response\AllClear::class,
				null
			],
			[
				'DELETE',
				'delete/arch/version/1.0.0/provider/test/arm64',
				Response\AllClear::class,
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
	}

	// provider create
	public function xtestCreateProviderReturnsBadRequest()
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
			'name'       => 'virtualtest',
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
		self::assertMessageBodyEqualsJsonArray($response, null);
		self::assertEquals('upload-data', file_get_contents($this->fs->url() . '/data/storage/test/something/1.0.0/virtualtest-unknown.box'));

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

	public function testDeleteReturnsNotFoundOnNotExistingBox()
	{
		$response = $this->runApp(
			'DELETE',
			'/api/v1/box/test/something/version/1.0.0/provider/virtualtest'
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

	public function testAccessTokenAsHeaderValidates()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\napi_token=testing");

		unset($this->app);

		$response = $this->runApp('GET', '/api/v1/authenticate', null, null, 'testing');

		self::assertInstanceOf(Response\AllClear::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}

	public function testAccessTokenAsHeaderIsInvalid()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\napi_token=testing");

		unset($this->app);

		$response = $this->runApp('GET', '/api/v1/authenticate', null, null, 'nope');

		self::assertInstanceOf(Response\NotAuthorized::class, $response);

		file_put_contents($env, $old);
		unset($this->app);
	}

	public function testArchitectureUpload()
	{
		\MockPhpStream::register();

		file_put_contents('php://input', 'arch-data');
		$response = $this->runApp(
			'PUT',
			'/api/v1/box/test/something/version/1.0.0/provider/virtualtest/amd64/upload'
		);

		\MockPhpStream::restore();

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, null);
		self::assertEquals('arch-data', file_get_contents($this->fs->url() . '/data/storage/test/something/1.0.0/virtualtest-amd64.box'));

		$response->getBody()->close();
	}

	public function testUploadDirectReturnsPathAndCallback()
	{
		\MockPhpStream::register();

		file_put_contents('php://input', 'arch-data');
		$response = $this->runApp(
			'GET',
			'/api/v1/box/test/something/version/1.0.0/provider/virtualtest/amd64/upload/direct'
		);

		\MockPhpStream::restore();

		$path = "/api/v1/box/test/something/version/1.0.0/provider/virtualtest/amd64/upload";
		$signature = hash_hmac('sha256', "PUT\n{$path}", null);
		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, [
			'upload_path' => "http://localhost{$path}?X-Phagrancy-Signature=" . $signature,
			'callback'    => "http://localhost{$path}/confirm",
		]);

		$response->getBody()->close();
	}

	public function testDirectUpload()
	{
		$env = $this->fs->url() . '/.env';
		$old = file_get_contents($env);
		file_put_contents($env, "{$old}\napi_token=testing");

		unset($this->app);

		\MockPhpStream::register();

		$path = "/api/v1/box/test/direct/version/1.0.0/provider/virtualtest/amd64/upload";

		file_put_contents('php://input', 'arch-data');
		$response = $this->runApp(
			'PUT',
			"{$path}?X-Phagrancy-Signature=" . hash_hmac('sha256', "PUT\n{$path}", 'testing')
		);

		\MockPhpStream::restore();

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, null);
		self::assertEquals('arch-data', file_get_contents($this->fs->url() . '/data/storage/test/direct/1.0.0/virtualtest-amd64.box'));

		$response->getBody()->close();
	}

	public function testUploadConfirmReturnsSuccess()
	{
		$response = $this->runApp(
			'PUT',
			'/api/v1/box/arch/test/version/2.0.0/provider/test/amd64/upload/confirm'
		);

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, null);

		$response->getBody()->close();
	}

	public function testUploadConfirmReturnsNotUploaded()
	{
		$response = $this->runApp(
			'PUT',
			'/api/v1/box/arch/test/version/3.0.0/provider/test/amd64/upload/confirm'
		);

		self::assertInstanceOf(Response\Json::class, $response);
		self::assertMessageBodyEqualsJsonArray($response, ['errors' => ['not uploaded']]);

		$response->getBody()->close();
	}
}
