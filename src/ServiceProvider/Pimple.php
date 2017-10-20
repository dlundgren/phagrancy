<?php

/**
 * @file
 * Contains Phagrancy\ServiceProvider\Pimple
 */

namespace Phagrancy\ServiceProvider;

use josegonzalez\Dotenv\Loader;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Action;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Pimple
	implements ServiceProviderInterface
{
	/**
	 * @var array The environment variables
	 */
	private $env = [];

	/**
	 * @var string The root path to the project
	 */
	private $rootPath;

	public function __construct($rootPath)
	{
		$this->rootPath = $rootPath;
	}

	public function register(Container $di)
	{
		$envFile = "{$this->rootPath}/.env";
		if (file_exists($envFile)) {
			$envLoader = new Loader("{$this->rootPath}/.env");
			$envLoader->parse();

			$this->env = $envLoader->toArray();
		}

		$di['path.storage'] = $this->resolveStoragePath();

		// register repositories
		$di[Repository\Scope::class] = function ($c) {
			return new Repository\Scope($c['path.storage']);
		};

		$di[Repository\Box::class] = function ($c) {
			return new Repository\Box($c['path.storage']);
		};

		// action handlers
		$di[Action\Scope\Index::class] = function ($c) {
			return new Action\Scope\Index($c[Repository\Scope::class], new Input\Scope());
		};

		$di[Action\Scope\Box\Definition::class] = function ($c) {
			return new Action\Scope\Box\Definition($c[Repository\Box::class], new Input\Box());
		};

		$di[Action\Scope\Box\SendFile::class] = function ($c) {
			return new Action\Scope\Box\SendFile($c[Repository\Box::class], new Input\BoxUpload(), $c['path.storage']);
		};

		// API action handlers
		$di[Action\Api\Scope\Index::class] = function ($c) {
			return new Action\Api\Scope\Index($c[Repository\Scope::class], new Input\Scope());
		};

		$di[Action\Api\Scope\Box\Definition::class] = function ($c) {
			return new Action\Api\Scope\Box\Definition($c[Repository\Box::class], new Input\Box());
		};

		$di[Action\Api\Scope\Box\Upload::class] = function ($c) {
			return new Action\Api\Scope\Box\Upload($c[Repository\Box::class], new Input\BoxUpload(), $c['path.storage']);
		};
	}

	private function resolveStoragePath()
	{
		$path = "data/storage";
		if (isset($this->env['storage_path'])) {
			$path = $this->env['storage_path'];
		}

		if ($path[0] !== '/') {
			$path = "{$this->rootPath}/{$path}";
		}

		return $path;
	}
}