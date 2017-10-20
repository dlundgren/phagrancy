<?php

namespace Phagrancy\Model\Entity;

class Scope
{
	private $name;
	private $boxes = [];

	public function __construct($name, $boxes)
	{
		$this->name  = $name;
		$this->boxes = $boxes;
	}

	public function name()
	{
		return $this->name;
	}

	public function boxes()
	{
		return $this->boxes;
	}

}