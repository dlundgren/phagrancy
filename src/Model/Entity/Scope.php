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
	private string $name;

	private array $boxes;

	public function __construct(string $name, array $boxes = [])
	{
		$this->name  = $name;
		$this->boxes = $boxes;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function boxes(): array
	{
		return $this->boxes;
	}

}