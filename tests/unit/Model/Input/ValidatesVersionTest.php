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
	protected $validator;

	public function setUp(): void
	{
		$this->validator = new class {
			use ValidatesVersion;
		};
	}

	public function provideGoodVersions()
	{
		return [
			['1'],
			['20180609'],
			['20180609.13425'],
			['1.2.3'],
			['1.2.3-alpha'],
			['1.2.3-alpha-build'],
			['1-beta'],
			['1.2-charlie'],
			['0.1']
		];
	}

	public function provideBadVersions()
	{
		return [
			['1.-1'],
			['v5'],
			['1.alpha_a'],
			['1.2.3.alpha'],
			['1.2.3.0+b'],
			['1.2.3-alpha build'],
			['1.2.3-alpha.10.beta.0+build.unicorn.rainbow'],
		];
	}

	/**
	 * @dataProvider provideGoodVersions
	 */
	public function testOkVersions($version)
	{
		self::assertEmpty($this->validator->validateVersion()($version));
	}

	/**
	 * @dataProvider provideBadVersions
	 */
	public function testBadVersions($version)
	{
		self::assertNotEmpty($this->validator->validateVersion()($version));
	}

	public function createValidator() {}
}