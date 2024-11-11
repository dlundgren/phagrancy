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
	private string $name;

	private string $scope;

	private array $versions;

	public function __construct(string $name, string $scope, array $versions)
	{
		$this->name     = $name;
		$this->scope    = $scope;
		$this->versions = $versions;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function path(): string
	{
		return "{$this->scope}/{$this->name}";
	}

	public function scope(): string
	{
		return $this->scope;
	}

	public function versions(): array
	{
		return $this->versions;
	}
}