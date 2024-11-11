<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\Definition
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to return the box definition
 *
 * @package Phagrancy\Action\Scope\Box
 */
class Definition
{
	private Repository\Box $boxes;

	private Input\Box $input;

	public function __construct(Repository\Box $boxes, Input\Box $input)
	{
		$this->boxes = $boxes;
		$this->input = $input;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$vagrant = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($vagrant->isValid()) {
			$box = $this->boxes->ofNameInScope($vagrant->name, $vagrant->scope);
		}

		return isset($box)
			? new Response\BoxDefinition($box, $request->getUri())
			: new Response\NotFound();
	}
}