<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Error
 */

namespace Phagrancy\Http\Response;

/**
 * Error response
 *
 * @package Phagrancy\Http\Response
 */
class Error
	extends Json
{
	public function __construct(...$errors)
	{
		parent::__construct(['errors' => $errors], 409);
	}
}