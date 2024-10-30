<?php

/**
 * @file
 * Contains Phagrancy\ServiceProvider\Pimple
 */

namespace Phagrancy\ServiceProvider;

use josegonzalez\Dotenv\Loader;
use Phagrancy\Action;
use Phagrancy\Http\Middleware;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Pimple Container Service Provider
 *
 * @package Phagrancy\ServiceProvider
 */
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

	/**
	 * @return mixed
	 */
	private function loadEnv()
	{
		$envFile = "{$this->rootPath}/.env";
		if (file_exists($envFile)) {
			$envLoader = new Loader("{$this->rootPath}/.env");
			$envLoader->parse();

			$this->env = $envLoader->toArray();
		}
		foreach (['api_token','storage_path','access_token','access_password'] as $var) {
			$value = getenv("PHAGRANCY_" . strtoupper($var));
			$this->env[$var] = empty($value) ? ($this->env[$var] ?? null) : $value;
		}

		return $this->env;
	}

	public function register(Container $di): void
	{
		$envFile = "{$this->rootPath}/.env";
		if (file_exists($envFile)) {
			$envLoader = new Loader("{$this->rootPath}/.env");
			$envLoader->parse();

			$this->env = $envLoader->toArray();
		}

		$di['env']          = $this->env = $this->loadEnv();
		$di['path.storage'] = $this->resolveStoragePath();

		// Authorization middleware
		$di[Middleware\ValidateAccessToken::class] = function ($c) {
			return new Middleware\ValidateAccessToken(isset($c['env']['api_token']) ? $c['env']['api_token'] : null);
		};

		$di[Middleware\ValidateTokenOrPassword::class] = function ($c) {
			return new Middleware\ValidateTokenOrPassword(
				isset($c['env']['access_token']) ? $c['env']['access_token'] : null,
				isset($c['env']['access_password']) ? $c['env']['access_password'] : null
			);
		};

		// register repositories
		$di[Repository\Scope::class] = function ($c) {
			return new Repository\Scope($c['path.storage']);
		};

		$di[Repository\Box::class] = function ($c) {
			return new Repository\Box($c['path.storage']);
		};

		// action handlers
		$di[Action\Scopes::class] = function ($c) {
			return new Action\Scopes($c[Repository\Scope::class], new Input\Scope());
		};

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

		$di[Action\Api\Scope\Box\CreateVersion::class] = function () {
			return new Action\Api\Scope\Box\CreateVersion(new Input\BoxVersion());
		};

		$di[Action\Api\Scope\Box\Upload::class] = function ($c) {
			return new Action\Api\Scope\Box\Upload(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['path.storage']
			);
		};

		$di[Action\Api\Scope\Box\UploadConfirm::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadConfirm(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['path.storage']
			);
		};

		$di[Action\Api\Scope\Box\UploadDirect::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadDirect(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['path.storage'],
				$c['env']['api_token'] ?? null
			);
		};

		$di[Action\Api\Scope\Box\UploadPreFlight::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadPreFlight(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['path.storage']
			);
		};

		$di[Action\Api\Scope\Box\Delete::class] = function ($c) {
			return new Action\Api\Scope\Box\Delete(
				$c[Repository\Box::class],
				new Input\BoxDelete(),
				$c['path.storage']
			);
		};

		$di[Action\Api\Scope\Box\SendFile::class] = function ($c) {
			return new Action\Api\Scope\Box\SendFile(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['path.storage']
			);
		};
	}

	private function resolveStoragePath(): string
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
