<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadTest
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Input\BoxUpload;
use Phagrancy\Model\Repository\Box;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class UploadTest
	extends ScopeTestCase
{
	private function uploadBox(string $content)
	{
		\MockPhpStream::register();
		file_put_contents('php://input', $content);

		$request = $this->buildRequest();
		$request->getAttribute('route')
				->setArguments(
					[
						'scope'    => 'test',
						'name'     => 'upload',
						'version'  => '1.0',
						'provider' => 'test'
					]);

		// build the action itself
		$action = new Upload(
			new Box($this->fs->url()),
			new BoxUpload(),
			$this->fs->url()
		);
		$response = $action($request);

		\MockPhpStream::restore();

		return $response;
	}

	public function testReturnsOkForExistingBox()
	{
		$response = $this->uploadBox('uploading');

		self::assertEquals('uploading', file_get_contents($this->fs->url() . '/test/upload/1.0/test-unknown.box'));
		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 200);

		$response->getBody()->close();
	}

	public function testReturnsErrorForAlreadyUploadedBox()
	{
		$this->testReturnsOkForExistingBox();

		$response = $this->uploadBox('override box');

		self::assertNotEquals('override box', file_get_contents($this->fs->url() . '/test/upload/1.0/test-unknown.box'));
		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 409);

		// test we get {"errors":["box already exists: test\/upload\/test\/unknown"]}

		$response->getBody()->close();
	}
}