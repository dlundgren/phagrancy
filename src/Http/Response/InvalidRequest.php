<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\InvalidParameters
 */

namespace Phagrancy\Http\Response;

/**
 * HTTP 422/Unprocessable Entity Request
 *
 * Use this when your validation has gone awry and you need to tell the user why
 *
 * @package Phagrancy\Http\Response
 */
class InvalidRequest
	extends Json
{
	public function __construct($errors)
	{
		parent::__construct(['error' => 422, 'message' => 'unprocessable entity', 'errors' => $errors], 422);
	}
}