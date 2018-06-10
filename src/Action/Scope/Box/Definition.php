<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\Definition
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to return the box definition
 *
 * @package Phagrancy\Action\Scope\Box
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

		return new Response\BoxDefinition($box, $request->getUri());
	}
}