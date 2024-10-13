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
	use ValidatesToken {
		ValidatesToken::validateToken as private _validateToken;
	}

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

	protected function validateToken(Request $request)
	{
		if (preg_match('#/upload$#', $path = $request->getUri()->getPath())) {
			if (
				$this->token &&
				$request->getQueryParam('X-Phagrancy-Signature') === hash_hmac('sha256', "PUT\n$path", $this->token)
			) {
				return true;
			}
		}

		return $this->_validateToken($request);
	}
}