<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadTest
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotFound;
use Phagrancy\Model\Input\BoxDelete;
use Phagrancy\Model\Repository\Box;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class DeleteTest
	extends ScopeTestCase
{
	public function testNotFoundReturned()
	{
		$request = $this->buildRequest();
		$request->getAttribute('route')
			->setArguments(
				[
					'scope'    => 'test',
					'name'     => 'kakaw',
					'version'  => '1.0.0',
					'provider' => 'test'
				]
			);

		// build the action itself
		$action = new Delete(
			new Box($this->storage),
			new BoxDelete(),
			$this->fs->url()
		);

		self::assertInstanceOf(NotFound::class, $action($request));
	}

	public function testReturnsUnableToDelete()
	{
		chmod(
			$this->fs->url() . '/test/delete/1.0.0/test.box',
			0000
		);
		$request = $this->buildRequest();
		$request->getAttribute('route')
			->setArguments(
				[
					'scope'    => 'test',
					'name'     => 'delete',
					'version'  => '1.0.0',
					'provider' => 'test'
				]
			);

		// build the action itself
		$action = new Delete(
			new Box($this->storage),
			new BoxDelete(),
			$this->fs->url()
		);

		$response = $action($request);
		self::assertResponseHasStatus($response, 409);
		self::assertMessageBodyEqualsJsonArray(
			$response,
			['errors' => 'unable to delete']
		);
	}

	public function testReturnsOkAndRemovesExistingBox()
	{
		$request = $this->buildRequest();
		$request->getAttribute('route')
			->setArguments(
				[
					'scope'    => 'test',
					'name'     => 'delete',
					'version'  => '1.0.0',
					'provider' => 'test'
				]
			);

		// build the action itself
		$action = new Delete(
			new Box($this->storage),
			new BoxDelete(),
			$this->fs->url()
		);

		// box file exists before action
		self::assertTrue(file_exists($this->fs->url() . '/test/delete/1.0.0/test.box'));

		$response = $action($request);

		// file was removed
		self::assertFalse(file_exists($this->fs->url() . '/test/delete/1.0.0/test.box'));
		self::assertInstanceOf(Json::class, $response);
		self::assertResponseHasStatus($response, 200);

		$response->getBody()->close();
	}
}
