<?php

/**
 * @file
 * Contains Phagrancy\Action\Scopes
 */

namespace Phagrancy\Action;

use Phagrancy\Http\Response;
use Phagrancy\Model\Repository;

/**
 * Action for listing the boxes in a given scope
 *
 * @package Phagrancy\Action
 */
class Scopes
{
	private Repository\Scope $repository;

	public function __construct(Repository\Scope $repository)
	{
		$this->repository = $repository;
	}

	public function __invoke()
	{
		return new Response\ScopeList($this->repository->all());
	}
}
