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
class ValidatePassword
{
	/**
	 * @var string The users and associated passwords
	 */
	private $password;

	private $forceSecure = false;

	public function __construct($password, $forceSecure)
	{
		$this->password    = $password;
		$this->forceSecure = false;
	}

	public function __invoke(Request $request, Response $response, $next)
	{
		if (empty($this->password)) {
			return $next($request, $response);
		}

		$ary  = explode(':', $request->getUri()->getUserInfo());
		$pass = isset($ary[1]) ? $ary[1] : null;

		if ($this->password === $pass) {
			return $next($request, $response);
		}

		return new NotAuthorized();
	}
}