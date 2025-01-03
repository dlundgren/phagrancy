<?php

/**
 * @file
 * Contains Phagrancy\TestCase\Integration
 */

namespace Phagrancy\TestCase;

use Helmich\Psr7Assert\Psr7Assertions;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Phagrancy\App;
use Phagrancy\Service\Storage;
use Phagrancy\ServiceProvider\Pimple;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Stream;

abstract class Integration
	extends TestCase
{
	use Psr7Assertions;

	protected $spec = [
		'.env' => 'storage_path=data/storage',
		'data' => [
			'storage' => [
				'arch' => [
					'test' => [
						'2.0.0' => [
							'test-amd64.box' => 'test',
						]
					],
				],
				'test' => [
					'test' => [
						'200'   => [
							'test.box' => 'test'
						],
						'2.0.0' => [
							'test.box' => 'test'
						],
					]
				],
				"alt"  => [
					"test" => [
						'1.0'     => [
							'virtualbox.box' => 'virtualbox'
						],
						'2.0.0.2' => [
							'vmware.box' => 'vmware'
						]
					]
				],
				'delete' => [
					'test' => [
						'1.0.0' => [
							'test.box' => 'testcontent'
						]
					],
					'arch' => [
						'1.0.0' => [
							'test-arm64.box' => 'testcontent'
						]
					]
				]
			]
		]
	];

	protected vfsStreamDirectory $fs;

	protected App $app;

	protected Storage $storage;

	protected function setUp(): void
	{
		$this->fs = vfsStream::setup('integration', null, $this->spec);
		$path = $this->fs->url() . "/data/storage";
		// we need to remove the locking as it's not supported on vfsStream special fs
		$lfa = new LocalFilesystemAdapter($path, null, 0);
		$this->storage = new Storage(new Filesystem($lfa), $path);
	}

	/**
	 * Runs the application and returns the response
	 *
	 * @param      $method
	 * @param      $path
	 * @param null $body
	 * @param bool $bodyIsJson
	 * @oaram string $token
	 * @return ResponseInterface
	 * @throws \Slim\Exception\MethodNotAllowedException
	 * @throws \Slim\Exception\NotFoundException
	 */
	protected function runApp($method, $path, $body = null, $bodyIsJson = false, $token = null)
	{
		$env = [
			'REQUEST_METHOD' => $method,
			'REQUEST_URI'    => $path,
		];
		if (isset($this->env)) {
			$env = array_merge($this->env, $env);
		}
		if ($bodyIsJson) {
			$env['CONTENT_TYPE'] = 'application/json';
		}

		if ($token) {
			$env['HTTP_AUTHORIZATION'] = "Bearer {$token}";
		}

		$request = Request::createFromEnvironment(Environment::mock($env));

		if (!empty($body)) {
			$streamFile = $this->fs->url() . '/request-body';
			file_put_contents($streamFile, $body);
			$request = $request->withBody(new Stream(fopen($streamFile, 'r')));
		}

		if (!isset($this->app)) {
			$container = new Container();
			(new Pimple($this->fs->url()))->register($container);

			$this->app = new App($container);
		}
		$container = $this->app->getContainer();
		$container['request'] = $request;
		$container['storage.box'] = $this->storage;

		return $this->app->run(true);
	}

	public static function assertMessageBodyEqualsJsonArray(ResponseInterface $response, $json)
	{
		self::hasHeader('content-type', Assert::matchesRegularExpression(',^application/json(;.+)?$,'));

		$b = $response->getBody();
		try {
			$b->rewind();
		}
		catch (\RuntimeException $e) {
			// most likely due to mocking
		}

		self::assertEquals($json, json_decode($b->getContents(), true));
	}

	public static function printVFS()
	{
		print_r(vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure());
	}
}
