<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateProviderTest
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Input\BoxProvider;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class CreateProviderTest
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
				'name'       => 'test',
				'upload_url' => 'http://localhost/api/v1/box/test/test/version/2.0/provider/test/upload'
			]
		);
	}

	protected function runAction($scope, $name, $version, $provider)
	{
		$action = new CreateProvider(
			new BoxProvider()
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

		$request = $request->withParsedBody(['provider' => ['name' => $provider]]);

		return $action($request);
	}
}