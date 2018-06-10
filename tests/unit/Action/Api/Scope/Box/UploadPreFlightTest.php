<?php

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Json;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class UploadPreFlightTest
	extends ScopeTestCase
{
	public function testReturnsOkForExistingBox()
	{
		$response = $this->runAction('test', 'test', '2.0', 'test');

		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageBodyMatchesJson(
			$response,
			[
				'upload_path' => 'http://localhost/api/v1/box/test/test/version/2.0/provider/test/upload'
			]
		);
	}

	protected function runAction($scope, $name, $version, $provider)
	{
		$action = new UploadPreFlight();

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