<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\ScopeList
 */

namespace Phagrancy\Http\Response;

/**
 * Scope JSON list response
 *
 * @package Phagrancy\Http\Response
 */
class ScopeList
	extends Json
{
	public function __construct(array $scopes)
	{
		parent::__construct($scopes);
	}
}
