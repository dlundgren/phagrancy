<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\IndexTest
 */

namespace Phagrancy\Action\Api\Scope;

use Phagrancy\Http\Response\Api\BoxList;
use Phagrancy\Http\Response\NotFound;
use Phagrancy\TestCase\Scope as ScopeTestCase;
use Phagrancy\Model\Repository\Scope as ScopeRepository;
use Phagrancy\Model\Input\Scope as ScopeInput;

class IndexTest
	extends ScopeTestCase
{
	public function testReturnsOkResponse()
	{
		$response = $this->runAction('test');

		self::assertInstanceOf(BoxList::class, $response);
		self::assertResponseHasStatus($response, 200);
		self::assertMessageBodyEqualsJsonArray(
			$response,
			[
				'username' => 'test',
				'boxes' => [
					'delete',
					'multi',
					'test'
				]
			]
		);
	}

	public function testReturns404()
	{
		$response = $this->runAction('nope');

		self::assertInstanceOf(NotFound::class, $response);
	}

	protected function runAction($scope)
	{
		$action = new Index(
			new ScopeRepository($this->fs->url()),
			new ScopeInput()
		);

		$request = $this->buildRequest();
		$request->getAttribute('route')->setArguments(['scope' => $scope]);

		$response = $action($request);

		return $response;
	}
}