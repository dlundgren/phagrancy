<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Json
 */

namespace Phagrancy\Http\Response;

use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * Generic JSON response
 *
 * @package Phagrancy\Http\Response
 */
class Json
	extends Response
{
	public function __construct($json = null, $code = 200)
	{
		$responseHeaders = new Headers();
		$responseHeaders->add('Content-Type', 'application/json');

		parent::__construct($code, $responseHeaders);

		$this->getBody()->write(json_encode($json));
	}
}