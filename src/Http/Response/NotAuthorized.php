<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\NotAuthorized
 */

namespace Phagrancy\Http\Response;

/**
 * HTTP Not Authorized Response
 *
 * @package Phagrancy\Http\Response
 */
class NotAuthorized
	extends Json
{
	public function __construct()
	{
		parent::__construct(['error' => 401, 'message' => 'not authorized'], 401);
	}
}