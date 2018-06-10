<?php

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response\BoxDefinition;
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
		self::assertMessageBodyEqualsJsonArray($response, $this->testTestJson);
	}

	public function testReturnsOkForNonExistentBox()
	{
		$response = $this->runAction('test', 'nope');

		self::assertInstanceOf(BoxDefinition::class, $response);
		self::assertMessageBodyEqualsJsonArray(
			$response,
			['name' => 'test/nope', 'versions' => []]
		);
	}

	public function testReturnsBoxData()
	{
		$response = $this->runAction('test', 'multi');

		self::assertInstanceOf(BoxDefinition::class, $response);
		self::assertMessageBodyEqualsJsonArray(
			$response,
			[
				'name'     => 'test/multi',
				'versions' => [
					[
						'version'   => '1',
						'providers' => [
							[
								'name' => '1',
								'url'  => 'http://localhost/test/multi/1/1'
							]
						]
					],
					[
						'version'   => '1.1',
						'providers' => [
							[
								'name' => 'test',
								'url'  => 'http://localhost/test/multi/1.1/test'
							]
						]
					]

				]
			]
		);
	}

	protected function runAction($scope, $name)
	{
		$action = new Definition(
			new BoxRepository($this->fs->url()),
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