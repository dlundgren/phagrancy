<?php

/**
 * @file
 * Contains Phagrancy\Model\Entity\Scope
 */

namespace Phagrancy\Model\Entity;

/**
 * Scope Entity
 *
 * @package Phagrancy\Model\Entity
 */
class Scope
{
	/**
	 * @var string The name of the scope
	 */
	private $name;

	/**
	 * @var array List of boxes that belong to this scope
	 */
	private $boxes = [];

	public function __construct($name, $boxes)
	{
		$this->name  = $name;
		$this->boxes = $boxes;
	}

	/**
	 * @return string The scope name
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * @return array The scopes boxes
	 */
	public function boxes()
	{
		return $this->boxes;
	}

}