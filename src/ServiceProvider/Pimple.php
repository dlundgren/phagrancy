<?php

/**
 * @file
 * Contains Phagrancy\ServiceProvider\Pimple
 */

namespace Phagrancy\ServiceProvider;

use josegonzalez\Dotenv\Loader;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Phagrancy\Action;
use Phagrancy\Http\Middleware;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Service\Storage;
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
	private array $env = [];

	private string $rootPath;

	public function __construct(string $rootPath)
	{
		$this->rootPath = $rootPath;
	}

	public function register(Container $di): void
	{
		$di['env']          = $this->env = $this->loadEnv();

		// Authorization middleware
		$di[Middleware\ValidateAccessToken::class] = function ($c) {
			return new Middleware\ValidateAccessToken($c['env']['api_token'] ?? null);
		};

		$di[Middleware\ValidateTokenOrPassword::class] = function ($c) {
			return new Middleware\ValidateTokenOrPassword(
				$c['env']['access_token'] ?? null,
				$c['env']['access_password'] ?? null
			);
		};

		// storage
		$di['storage.box'] = function () {
			$path = $this->resolvePathFromEnv('storage_path', $this->rootPath);

			return new Storage(new Filesystem(new LocalFilesystemAdapter($path)), $path);
		};

		// register repositories
		$di[Repository\Scope::class] = function ($c) {
			return new Repository\Scope($c['storage.box']);
		};

		$di[Repository\Box::class] = function ($c) {
			return new Repository\Box($c['storage.box']);
		};

		// action handlers
		$di[Action\Scopes::class] = function ($c) {
			return new Action\Scopes($c[Repository\Scope::class]);
		};

		$di[Action\Scope\Index::class] = function ($c) {
			return new Action\Scope\Index($c[Repository\Scope::class], new Input\Scope());
		};

		$di[Action\Scope\Box\Definition::class] = function ($c) {
			return new Action\Scope\Box\Definition($c[Repository\Box::class], new Input\Box());
		};

		$di[Action\Scope\Box\SendFile::class] = function ($c) {
			return new Action\Scope\Box\SendFile($c[Repository\Box::class], new Input\BoxUpload(), $c['storage.box']);
		};

		// API action handlers
		$di[Action\Api\Scope\Index::class] = function ($c) {
			return new Action\Api\Scope\Index($c[Repository\Scope::class], new Input\Scope());
		};

		$di[Action\Api\Scope\Box\Definition::class] = function ($c) {
			return new Action\Api\Scope\Box\Definition($c[Repository\Box::class], new Input\Box());
		};

		$di[Action\Api\Scope\Box\CreateProvider::class] = function () {
			return new Action\Api\Scope\Box\CreateProvider(new Input\BoxProvider());
		};

		$di[Action\Api\Scope\Box\CreateVersion::class] = function () {
			return new Action\Api\Scope\Box\CreateVersion(new Input\BoxVersion());
		};

		$di[Action\Api\Scope\Box\Upload::class] = function ($c) {
			return new Action\Api\Scope\Box\Upload(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['storage.box']
			);
		};

		$di[Action\Api\Scope\Box\UploadConfirm::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadConfirm(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['storage.box']
			);
		};

		$di[Action\Api\Scope\Box\UploadDirect::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadDirect(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['storage.box'],
				$c['env']['api_token'] ?? null
			);
		};

		$di[Action\Api\Scope\Box\UploadPreFlight::class] = function ($c) {
			return new Action\Api\Scope\Box\UploadPreFlight(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['storage.box']
			);
		};

		$di[Action\Api\Scope\Box\Delete::class] = function ($c) {
			return new Action\Api\Scope\Box\Delete(
				$c[Repository\Box::class],
				new Input\BoxDelete(),
				$c['storage.box']
			);
		};

		$di[Action\Api\Scope\Box\SendFile::class] = function ($c) {
			return new Action\Api\Scope\Box\SendFile(
				$c[Repository\Box::class],
				new Input\BoxUpload(),
				$c['storage.box']
			);
		};
	}

	private function loadEnv(): array
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

	private function resolvePathFromEnv(string $variable, string $defaultPath): string
	{
		$path = $this->env[$variable] ?? $defaultPath;

		return $path[0] === '/'
			? $path
			: "{$this->rootPath}/{$path}";
	}
}
