<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Index
 */

namespace Phagrancy\Action\Api\Scope;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for listing the boxes in a given scope
 *
 * @package Phagrancy\Action\Api\Scope
 */
class Index
{
	private Repository\Scope $repository;

	private Input\Scope $input;

	public function __construct(Repository\Scope $repository, Input\Scope $input)
	{
		$this->repository = $repository;
		$this->input      = $input;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$vagrant = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($vagrant->isValid()) {
			$scope   = $this->repository->ofName($vagrant->scope);
		}

		return isset($scope)
			? new Response\Api\BoxList($scope)
			: new Response\NotFound();
	}
}