<?php

namespace Phagrancy\TestCase;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

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
			]
		]
	];

	protected $fs;

	protected function setUp()
	{
		$this->fs = vfsStream::setup('scope', null, $this->scope);
	}

}