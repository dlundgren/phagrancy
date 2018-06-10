<?php

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotFound;
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

	protected function runAction($scope, $name, $version)
	{
		$action = new CreateVersion();

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