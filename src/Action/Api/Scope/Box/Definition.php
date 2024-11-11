<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Definition
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to return the box definition
 *
 * @NOTE because we do not allow "creation" of scopes/boxes, we MUST return a valid Box Definition, otherwise packer
 *       will croak
 *
 * @package Phagrancy\Action\Api\Scope\Box
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
		$input = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($input->isValid()) {
			$box = $this->boxes->ofNameInScope($input->name, $input->scope);
		}

		return isset($box)
			? new Response\Api\BoxDefinition($box, $request->getUri())
			: new Response\NotFound();
	}
}