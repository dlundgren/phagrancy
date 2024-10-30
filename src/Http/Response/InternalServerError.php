<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\InternalServerError
 */

namespace Phagrancy\Http\Response;

/**
 * HTTP 500/Internal Server Error
 *
 * @package Phagrancy\Http\Response
 */
class InternalServerError
	extends Json
{
	public function __construct(string $message)
	{
		parent::__construct(['error' => 500, 'message' => $message], 500);
	}
}