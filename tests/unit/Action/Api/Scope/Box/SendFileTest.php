<?php

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\NotFound;
use Phagrancy\Model\Input\BoxUpload;
use Phagrancy\TestCase\Scope as ScopeTestCase;
use Phagrancy\Model\Repository\Box as BoxRepository;
use Slim\Http\Response;

class SendFileTest
	extends ScopeTestCase
{
	public function testReturnsOkForExistingBox()
	{
		$response = $this->runAction('test', 'test', '1.0', 'test');

		self::assertInstanceOf(Response::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageHasHeader($response, 'Content-Type', 'application/octet-stream');
		self::assertMessageHasHeader($response, 'Content-Disposition', 'attachment; filename="test-test-1.0.box"');

		// not testing binary, but this should be sufficient to ensure we have the content
		self::assertEquals('body', $response->getBody()->getContents());

		$response->getBody()->close(); // close the file descriptor
	}

	public function testReturnsNotFoundForNonExistentBox()
	{
		$response = $this->runAction('test', 'nope', '1.0', 'test');

		self::assertInstanceOf(NotFound::class, $response);
	}

	protected function runAction($scope, $name, $version, $provider)
	{
		$action = new SendFile(
			new BoxRepository($this->fs->url()),
			new BoxUpload(),
			$this->fs->url()
		);

		$request = $this->buildRequest();
		$request->getAttribute('route')
				->setArguments(
					[
						'scope'    => $scope,
						'name'     => $name,
						'version'  => $version,
						'provider' => $provider
					]);

		$response = $action($request);

		return $response;
	}
}