<?php

/**
 * @file
 * Contains Phagrancy\TestCase\Scope
 */

namespace Phagrancy\TestCase;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Phagrancy\Model\Repository\IdentityMap;
use Phagrancy\Service\Storage;

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

	protected Storage $storage;

	protected function setUp(): void
	{
		IdentityMap::clear();
		$this->fs = vfsStream::setup('scope', null, $this->scope);

		$path = $this->fs->url();
		$this->storage = new Storage(new Filesystem(
			new LocalFilesystemAdapter(
				$path,
				null,
				// Remove write lock since it's not supported on vfsStream
				0
			)), $path);
	}
}
