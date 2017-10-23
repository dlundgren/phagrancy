<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\NotAuthorized
 */

namespace Phagrancy\Http\Response;

use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * HTTP Not Authorized Response
 *
 * @package Phagrancy\Http\Response
 */
class NotAuthorized
	extends Response
{
	public function __construct()
	{
		$headers = new Headers();
		$headers->add('Content-Type', 'application/json');

		parent::__construct(401, $headers);

		$this->getBody()->write(json_encode(['error' => 401, 'message' => 'not authorized']));
	}
}