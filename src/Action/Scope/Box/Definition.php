<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\Definition
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
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
	private $validator;

	public function __construct(Repository\Box $boxes, Input\Box $validator)
	{
		$this->boxes     = $boxes;
		$this->validator = $validator;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$params = $this->validator->validate($request->getAttribute('route')->getArguments());
		$box    = $this->boxes->ofNameInScope($params['name'], $params['scope']);

		return new Response\BoxDefinition($box, $request->getUri());
	}
}