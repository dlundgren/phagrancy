<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Index
 */

namespace Phagrancy\Action\Api\Scope;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for listing the boxes in a given scope
 *
 * @package Phagrancy\Action\Api\Scope
 */
class Index
{
	/**
	 * @var Repository\Scope
	 */
	private $repository;

	/**
	 * @var Input\Scope
	 */
	private $input;

	public function __construct(Repository\Scope $repository, Input\Scope $input)
	{
		$this->repository = $repository;
		$this->input      = $input;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$params = $this->input->validate($request->getAttribute('route')->getArguments());
		if (!$params) {
			return new Response\NotFound();
		}

		$scope   = $this->repository->ofName($params['scope']);
		if (!$scope) {
			return new Response\NotFound();
		}

		return new Response\Api\BoxList($scope);
	}
}