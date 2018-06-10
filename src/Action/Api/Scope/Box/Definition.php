<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Definition
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to return the box definition
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Definition
{
	/**
	 * @var Repository\Box
	 */
	private $boxes;

	/**
	 * @var Input\Box
	 */
	private $input;

	public function __construct(Repository\Box $boxes, Input\Box $input)
	{
		$this->boxes = $boxes;
		$this->input = $input;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$params = $this->input->validate($request->getAttribute('route')->getArguments());
		$box    = $this->boxes->ofNameInScope($params['name'], $params['scope']);

		return new Response\Api\BoxDefinition($box, $request->getUri());
	}
}