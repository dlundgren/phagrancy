<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidatesPassword
 */

namespace Phagrancy\Http\Middleware;

use Slim\Http\Request;

/**
 * Provides HTTP Basic Authentication password check
 *
 * @package Phagrancy\Http\Middleware
 */
trait ValidatesPassword
{
	private ?string $password;

	private function validatePassword(Request $request)
	{
		$ary = explode(':', $request->getUri()->getUserInfo());

		return empty($this->password) || $this->password === ($ary[1] ?? null);
	}
}