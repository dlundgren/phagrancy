<?php

/**
 * @file
 * Contains Phagrancy\Model\Repository\Scope
 */

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;

/**
 * Repository for the Scopes
 *
 * @package Phagrancy\Model\Repository
 */
class Scope
{
	/**
	 * @var string The path to the scopes
	 */
	private $path;

	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * @param string $name The name of the scope to find
	 * @return mixed|null|Entity\Scope The scope
	 */
	public function ofName($name)
	{
		if (empty($name)) {
			return null;
		}
		$scope = IdentityMap::get(Entity\Scope::class, $name);
		if (!$scope) {
			$dir = "{$this->path}/{$name}";
			if (!file_exists($dir)) {
				return null;
			}

			// load a list of the boxes for this user
			/** @var $file \SplFileInfo */
			$boxes = [];
			foreach(new \FilesystemIterator($dir) as $path => $file) {
				if ($file->isDir()) {
					$boxes[] = $file->getBasename();
				}
			}
			$scope = new Entity\Scope($name, $boxes);
			IdentityMap::set($scope, $name);
		}

		return $scope;
	}
}