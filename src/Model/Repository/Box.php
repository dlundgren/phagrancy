<?php

/**
 * @file
 * Contains Phagrancy\Model\Repository\Box
 */

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;
use Phagrancy\Service\Storage;

/**
 * Repository for the boxes
 *
 * @package Phagrancy\Model\Repository
 */
class Box
{
	private Storage $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	public function ofNameInScope(string $name, string $scope): ?Entity\Box
	{
		$key = "{$scope}.{$name}";
		$box = IdentityMap::get(Entity\Box::class, $key);
		if (!$box) {
			$path = "{$scope}/{$name}";
			$versions = [];
			foreach($this->storage->directories($path) as $version) {
				$providers = [];
				foreach($this->storage->files("{$path}/{$version}") as $provider) {
					if (str_ends_with($provider, '.box')) {
						$providers[] = explode('-', basename($provider, '.box'));
					}
				}
				if (!empty($providers)) {
					$versions[$version] = $providers;
				}
			}

			$box      = new Entity\Box($name, $scope, $versions);
			IdentityMap::set($box, $key);
		}

		return $box;
	}
}