<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\NotFound
 */

namespace Phagrancy\Http\Response;

/**
 * HTTP Not Found Response
 *
 * @package Phagrancy\Http\Response
 */
class NotFound
	extends Json
{
	public function __construct()
	{
		parent::__construct(['error' => 404, 'message' => 'not found'], 404);
	}
}