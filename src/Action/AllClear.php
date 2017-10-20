<?php

/**
 * @file
 * Contains Phagrancy\Action\AllClear
 */

namespace Phagrancy\Action;

use Psr\Http\Message\ResponseInterface;

/**
 * Sends a generic 200 Response
 *
 * @package Phagrancy\Action
 */
class AllClear
{
	public function __invoke($request, ResponseInterface $response)
	{
		return new \Slim\Http\Response();
	}
}