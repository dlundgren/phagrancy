<?php

namespace Phagrancy\Action\Api\Scope\Box;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Input\BoxUpload;
use Phagrancy\Model\Repository\Box;
use Phagrancy\TestCase\Scope as ScopeTestCase;
use Slim\Http\Stream;
use Slim\Tests\Http\BodyTest;

// due to an issue with tempnam not working with vfs' we need to make it work
function tempnam($path, $name) {
	return "{$path}/{$name}";
}

class UploadTest
	extends ScopeTestCase
{
	public function testReturnsOkForExistingBox()
	{
		\MockPhpStream::register();
		file_put_contents('php://input', 'uploading');

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

		self::assertEquals('uploading', file_get_contents($this->fs->url() . '/test/upload/1.0/test.box'));
		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 200);
	}
}