<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\NotFound
 */

namespace Phagrancy\Http\Response;

use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * HTTP Not Found Response
 *
 * @package Phagrancy\Http\Response
 */
class NotFound
	extends Response
{
	public function __construct()
	{
		$headers = new Headers();
		$headers->add('Content-Type', 'application/json');

		parent::__construct(404, $headers);

		$this->getBody()->write(json_encode(['error' => 404, 'message' => 'not found']));
	}
}