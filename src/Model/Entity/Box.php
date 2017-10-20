<?php
namespace Phagrancy\Model\Entity;

class Box
{
	private $name;
	private $scope;
	private $versions;

	public function __construct($name, $scope, $versions)
	{
		$this->name     = $name;
		$this->scope    = $scope;
		$this->versions = $versions;
	}

	public function name()
	{
		return $this->name;
	}
	public function path()
	{
		return "{$this->scope}/{$this->name}";
	}

	public function scope()
	{
		return $this->scope;
	}

	public function versions()
	{
		return $this->versions;
	}
}