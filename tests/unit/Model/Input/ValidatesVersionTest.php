<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\ValidatesVersionTest
 */

namespace Phagrancy\Model\Input;

use PHPUnit\Framework\TestCase;

class ValidatesVersionTest
	extends TestCase
{
	public function provideGoodVersions()
	{
		return [
			['1'],
			['20180609'],
			['20180609.13425'],
			['1.2.3'],
			['1.alpha_a'],
			['1.2.3.alpha'],
			['1.2.3.0+b'],
			['1.2.3-alpha.10.beta.0+build.unicorn.rainbow'],
		];
	}

	public function provideBadVersions()
	{
		return [
			['1.-1'],
		];
	}

	/**
	 * @dataProvider provideGoodVersions
	 */
	public function testOkVersions($version)
	{
		$c = new class() {
			use ValidatesVersion;
		};

		$f = $c->validateVersion();
		self::assertEmpty($f($version));
	}

	/**
	 * @dataProvider provideBadVersions
	 */
	public function testBadVersions($version)
	{
		$c = new class() {
			use ValidatesVersion;
		};

		$f = $c->validateVersion();
		self::assertNotEmpty($f($version));
	}

}