<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateVersionTest
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\InvalidRequest;
use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotFound;
use Phagrancy\Model\Input\BoxVersion;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class CreateVersionTest
	extends ScopeTestCase
{
	public function testReturnsOkForExistingBox()
	{
		$response = $this->runAction('test', 'test', '2.0');

		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageBodyMatchesJson(
			$response,
			['version' => "2.0", 'description' => 'description']
		);
	}

	public function testReturnsInvalidParameter()
	{
		$response = $this->runAction('test', 'test', '2.-1');

		self::assertInstanceOf(InvalidRequest::class, $response);
	}

	protected function runAction($scope, $name, $version)
	{
		$action = new CreateVersion(new BoxVersion());

		$request = $this->buildRequest();
		$request->getAttribute('route')
				->setArguments(
					[
						'scope'    => $scope,
						'name'     => $name
					]);

		$request = $request->withParsedBody(['version' => ['version' => $version, 'description' => 'description']]);

		$response = $action($request);

		return $response;
	}
}