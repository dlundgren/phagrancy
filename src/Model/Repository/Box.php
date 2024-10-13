<?php

/**
 * @file
 * Contains Phagrancy\Model\Repository\Box
 */

namespace Phagrancy\Model\Repository;

use Phagrancy\Model\Entity;

/**
 * Repository for the boxes
 *
 * @package Phagrancy\Model\Repository
 */
class Box
{
	/**
	 * @var string Path to the boxes
	 */
	private $path;

	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * @param $name
	 * @param $scope
	 * @return mixed|null|Entity\Box The Box
	 */
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

	/**
	 * Loads the versions from the box directory
	 *
	 * @param string $dir
	 * @return array List of versions
	 */
	private function loadVersions($dir)
	{
		$versions = [];
		foreach (new \FilesystemIterator($dir) as $path => $file) {
			/** @var $file \SplFileInfo */
			if ($file->isDir()) {
				$versions[$file->getBasename()] = $this->loadProviders($file->getPathname());
			}
		}

		return $versions;
	}

	/**
	 * Returns a list of providers from the version directory
	 *
	 * @param string $dir
	 * @return array List of providers
	 */
	private function loadProviders($dir)
	{
		$providers = [];
		foreach (new \FilesystemIterator($dir) as $path => $file) {
			/** @var $file \SplFileInfo */
			if ($file->getExtension() === 'box') {
				$providers[] = explode('-', $file->getBasename('.box'));
			}
		}

		return $providers;
	}
}