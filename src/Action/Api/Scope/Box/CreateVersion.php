<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateVersion
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to create a box version
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class CreateVersion
{

	public function __invoke(ServerRequestInterface $request)
	{
		$data = $request->getParsedBody();
		$r    = new \Slim\Http\Response();

		$r->getBody()->write(json_encode($data['version']));

		return $r;
	}
}