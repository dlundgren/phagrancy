<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateVersion
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to create a box version
 *
 * This just responds with the version information passed in to it
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class CreateVersion
{

	public function __invoke(ServerRequestInterface $request)
	{
		$data = $request->getParsedBody();
		if (empty($data)) {
			return new Response\BadRequest('No version supplied');
		}

		return new Response\Json($data['version']);
	}
}