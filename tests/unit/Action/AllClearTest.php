<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\AllClearTest
 */

namespace Phagrancy\Action;

use Helmich\Psr7Assert\Psr7Assertions;
use Phagrancy\TestCase\Action;

class AllClearTest
	extends Action
{
	use Psr7Assertions;

	public function testReturnsOkResponse()
	{
		$ac = new AllClear();

		self::assertResponseHasStatus($ac(), 200);
	}
}