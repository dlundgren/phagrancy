<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Index
 */

namespace Phagrancy\Action\Scope;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for listing the boxes in a given scope
 *
 * @package Phagrancy\Action\Scope
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
		$scope   = $this->repository->ofName($vagrant->scope ?? '');

		return $scope
			? new Response\BoxList($scope)
			: new Response\NotFound();
	}
}