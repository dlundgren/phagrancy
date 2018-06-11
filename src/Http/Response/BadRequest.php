<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\BadRequest
 */

namespace Phagrancy\Http\Response;

/**
 * HTTP Bad Request Response
 *
 * @package Phagrancy\Http\Response
 */
class BadRequest
	extends Json
{
	public function __construct($message = 'Bad Request')
	{
		parent::__construct(['error' => 400, 'message' => $message], 400);
	}
}