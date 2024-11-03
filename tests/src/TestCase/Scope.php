<?php

/**
 * @file
 * Contains Phagrancy\TestCase\Scope
 */

namespace Phagrancy\TestCase;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Phagrancy\Model\Repository\IdentityMap;

abstract class Scope
	extends Action
{
	protected $testTestJson = [
		'name'     => 'test/test',
		'versions' => [
			[
				'version'   => '1.0',
				'providers' => [
					[
						'name' => 'test',
						'url'  => 'http://localhost/test/test/1.0/test'
					]
				]
			]
		]
	];

	protected $scope = [
		'test' => [
			'test'  => [
				'1.0' => [
					'test.box' => 'body'
				]
			],
			'multi' => [
				'1'   => [
					'1.box' => '1'
				],
				'1.1' => [
					'test.box' => 'test'
				]
			],
			'delete' => [
				'1.0.0' => [
					'test.box' => 'testcontent'
				]
			]
		]
	];

	protected vfsStreamDirectory $fs;

	protected function setUp(): void
	{
		IdentityMap::clear();
		$this->fs = vfsStream::setup('scope', null, $this->scope);
	}
}
