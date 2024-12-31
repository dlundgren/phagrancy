<?php

/**
 * @file
 * Contains Phagrancy\Action\Scopes
 */

namespace Phagrancy\Action;

use Helmich\Psr7Assert\Psr7Assertions;
use Phagrancy\Model\Repository\Scope;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class ScopesTest
	extends ScopeTestCase
{
	use Psr7Assertions;

	public function testReturnsOkResponse()
	{
		$r = (new Scopes(new Scope($this->storage)))();

		self::assertResponseHasStatus($r, 200);
		self::assertMessageBodyEqualsJsonArray(
			$r,
			[
				'test'
			]
		);
	}
}