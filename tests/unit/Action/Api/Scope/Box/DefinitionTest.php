<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\DefinitionTest
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Api\BoxDefinition;
use Phagrancy\TestCase\Scope as ScopeTestCase;
use Phagrancy\Model\Repository\Box as BoxRepository;
use Phagrancy\Model\Input\Box as BoxInput;

class DefinitionTest
	extends ScopeTestCase
{
	public function testReturnsOkForExistingBox()
	{
		$response = $this->runAction('test', 'test');

		self::assertInstanceOf(BoxDefinition::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertResponseJsonEqualsString($response, 'versions.0.version', '1.0');
	}

	public function testReturnsOkForNonExistentBox()
	{
		$response = $this->runAction('test', 'nope');

		self::assertInstanceOf(BoxDefinition::class, $response);
	}

	protected function runAction($scope, $name)
	{
		$action = new Definition(
			new BoxRepository($this->storage),
			new BoxInput()
		);

		$request = $this->buildRequest();
		$request->getAttribute('route')
				->setArguments(
					[
						'scope' => $scope,
						'name'  => $name
					]);

		$response = $action($request);

		return $response;
	}
}