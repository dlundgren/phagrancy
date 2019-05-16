<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidateAccessToken
 */

namespace Phagrancy\Http\Middleware;

use Phagrancy\Http\Response\NotAuthorized;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Validates that the request is using the proper access_token
 *
 * @package Phagrancy\Http\Middleware
 */
class ValidateAccessToken
{
	use ValidatesToken;

	public function __construct($token)
	{
		$this->token = $token;
	}

	public function __invoke(Request $request, Response $response, $next)
	{
		return $this->validateToken($request)
			? $next($request, $response)
			: new NotAuthorized();
	}
}