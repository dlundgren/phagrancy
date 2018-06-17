<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\ScopeList
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Model\Entity;
use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * Scope JSON list response
 *
 * @package Phagrancy\Http\Response
 */
class ScopeList
	extends Response
{
	use RespondsWithJson;

	public function __construct(array $scopes)
	{
		$this->constructResponse($scopes);
	}
}
