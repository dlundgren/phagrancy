<?php

/**
 * @file
 * Contains Phagrancy\Http\Context\Vagrant
 */

namespace Phagrancy\Http\Context;

/**
 * Context for Vagrant boxes
 *
 * @property string $scope
 * @property string $name
 * @property string $version
 * @property string $provider
 * @property string $architecture
 *
 * @method string scope()
 * @method string name()
 * @method string version()
 * @method string provider()
 * @method string architecture()
 */
class Vagrant
{
	protected array $attributes = [];

	public function __construct() {}

	public static function createFromInput($attributes, $errors): self
	{
		$vagrant                       = new self;
		$vagrant->attributes           = is_array($attributes) ? $attributes : [];
		$vagrant->attributes['errors'] = $errors;

		return $vagrant;
	}

	public function __get($name)
	{
		return $this->attributes[$name] ?? null;
	}

	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	public function __call($name, $arguments)
	{
		return $this->attributes[$name] ?? null;
	}

	public function isValid(): bool
	{
		return empty($this->attributes['errors']);
	}

	public function errorPath()
	{
		$segments = [
			$this->scope,
			$this->name,
			$this->version,
			$this->provider,
			$this->architecture ?? 'unknown'
		];

		return join('/', array_filter($segments));
	}

	public function publicPath(): string
	{
		$architecture = $this->architecture ?? 'unknown';

		// must have scope/name/version/provider
		return join("/", [
			$this->scope,
			$this->name,
			$this->version,
			"{$this->provider}-{$architecture}.box"
		]);
	}

	public function apiPath(): string
	{
		$url = [];
		isset($this->attributes['scope']) && $url[] = $this->attributes['scope'];
		isset($this->attributes['name']) && $url[] = $this->attributes['name'];
		isset($this->attributes['version']) && $url[] = "version/{$this->attributes['version']}";
		isset($this->attributes['provider']) && $url[] = "provider/{$this->attributes['provider']}";

		// should we validate the architecture is approved? i386,amd64,aarch64
		isset($this->attributes['architecture']) && $url[] = $this->attributes['architecture'];

		return '/api/v1/box/' . join('/', $url);
	}
}