<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidatesToken
 */

namespace Phagrancy\Http\Middleware;

use Slim\Http\Request;

/**
 * Validates that the request is using the proper access_token
 *
 * @package Phagrancy\Http\Middleware
 */
trait ValidatesToken
{
	private ?string $token;

	private function getTokenFromRequest(Request $request): string
	{
		$token = $request->getQueryParam('access_token', '');
		if (empty($token) && !empty($token = $request->getHeaderLine('Authorization'))) {
			$tmp   = explode(' ', $token, 2);
			$token = $tmp[1];
		}

		return $token;
	}

	private function validateToken(Request $request): bool
	{
		return empty($this->token) || $this->getTokenFromRequest($request) === $this->token;
	}
}