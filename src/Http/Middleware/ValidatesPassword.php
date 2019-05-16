<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidatesPassword
 */

namespace Phagrancy\Http\Middleware;

use Phagrancy\Http\Response\NotAuthorized;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Provides HTTP Basic Authentication password check
 *
 * @package Phagrancy\Http\Middleware
 */
trait ValidatesPassword
{
	/**
	 * @var string|null The password
	 */
	private $password;

	/**
	 * Validates the password
	 *
	 * @param Request $request
	 * @return bool True if there is no password defined, or if the Request password matches
	 */
	private function validatePassword(Request $request)
	{
		if ($this->password === null) {
			return true;
		}

		$ary  = explode(':', $request->getUri()->getUserInfo());
		$pass = isset($ary[1]) ? $ary[1] : null;

		return $this->password === $pass;
	}
}