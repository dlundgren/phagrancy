<?php

/**
 * @file
 * Contains Phagrancy\Model\Entity\Box
 */

namespace Phagrancy\Model\Entity;

/**
 * Box Entity defines the box
 *
 * @package Phagrancy\Model\Entity
 */
class Box
{
	/**
	 * @var string The name of the box
	 */
	private $name;

	/**
	 * @var string The scope of the box
	 */
	private $scope;

	/**
	 * @var array The box versions
	 */
	private $versions;

	public function __construct($name, $scope, $versions)
	{
		$this->name     = $name;
		$this->scope    = $scope;
		$this->versions = $versions;
	}

	/**
	 * @return string The name of the box
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * @return string The path (tag) of the box
	 */
	public function path()
	{
		return "{$this->scope}/{$this->name}";
	}

	/**
	 * @return string The boxes scope
	 */
	public function scope()
	{
		return $this->scope;
	}

	/**
	 * @return array The versions of the box
	 */
	public function versions()
	{
		return $this->versions;
	}
}