<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidateTokenOrPassword
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
class ValidateTokenOrPassword
{
	use ValidatesToken, ValidatesPassword;

	public function __construct($token, $password)
	{
		$this->token    = $token;
		$this->password = $password;
	}

	public function __invoke(Request $request, Response $response, $next)
	{
		$hasAccessToken = $request->getQueryParam('access_token', false);
		$run            = $hasAccessToken
			? $this->validateToken($request)
			: $this->validatePassword($request);

		return $run
			? $next($request, $response)
			: new NotAuthorized();
	}
}