<?php

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;

class Box
{
	private $path;

	public function __construct($path)
	{
		$this->path = $path;
	}

	public function ofNameInScope($name, $scope)
	{
		$key = "{$scope}.{$name}";
		$box = IdentityMap::get(Entity\Box::class, $key);
		if (!$box) {
			$dir      = "{$this->path}/{$scope}/{$name}";
			$versions = file_exists($dir) ? $this->loadVersions($dir) : [];
			$box      = new Entity\Box($name, $scope, $versions);
			IdentityMap::set($box, $key);
		}

		return $box;
	}

	private function loadVersions($dir)
	{
		$versions = [];
		foreach (new \FilesystemIterator($dir) as $path => $file) {
			/** @var $file \SplFileInfo */
			if ($file->isDir()) {
				$versions[$file->getBasename()] = $this->loadProviders($file->getRealPath());
			}
		}

		return $versions;
	}

	private function loadProviders($dir)
	{
		$providers = [];
		foreach (new \FilesystemIterator($dir) as $path => $file) {
			/** @var $file \SplFileInfo */
			if ($file->getExtension() === 'box') {
				$providers[] = $file->getBasename('.box');
			}
		}

		return $providers;
	}
}