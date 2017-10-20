<?php

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;

class Scope
{
	private $path;
	public function __construct($path)
	{
		$this->path = $path;
	}

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