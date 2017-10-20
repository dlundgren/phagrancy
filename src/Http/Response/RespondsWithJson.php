<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\RespondsWithJson
 */

namespace Phagrancy\Http\Response;

use Slim\Http\Headers;

/**
 * Trait for constructing a JSON response
 *
 * @package Phagrancy\Http\Response
 */
trait RespondsWithJson
{
	/**
	 * Constructs a JSON response for the response
	 *
	 * @param       $data
	 * @param array $headers
	 */
	private function constructResponse($data, $headers = [])
	{
		$responseHeaders = new Headers();
		$responseHeaders->replace($headers);
		$responseHeaders->add('Content-Type', 'application/json');

		parent::__construct(200, $responseHeaders);

		$this->getBody()->write(json_encode($data));
	}
}