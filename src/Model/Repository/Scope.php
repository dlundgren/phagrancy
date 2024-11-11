<?php

/**
 * @file
 * Contains Phagrancy\Model\Repository\Scope
 */

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;
use Phagrancy\Service\Storage;

/**
 * Repository for the Scopes
 *
 * @package Phagrancy\Model\Repository
 */
class Scope
{
	private Storage $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @return string[] The scopes
	 */
	public function all(): array
	{
		return $this->storage->directories();
	}

	public function ofName(string $name): ?Entity\Scope
	{
		if (empty($name)) {
			return null;
		}

		$scope = IdentityMap::get(Entity\Scope::class, $name);
		if (!$scope) {
			if (!$this->storage->exists($name)) {
				return null;
			}

			IdentityMap::set(
				$scope = new Entity\Scope($name, $this->storage->directories($name)),
				$name
			);
		}

		return $scope;
	}
}