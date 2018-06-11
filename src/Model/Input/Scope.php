<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\Scope
 */

namespace Phagrancy\Model\Input;

use Psr\Http\Message\ServerRequestInterface;
use Validator\LIVR;

/**
 * Scope input & validation
 *
 * @package Phagrancy\Model\Input
 */
class Scope
{
	use IsValidator, ValidatesScope, ValidatesBoxName;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope' => [$this, 'validateScope'],
			]);
	}

	public function validate($params)
	{
		return $this->perform(
			$params,
			[
				'scope' => self::$SCOPE_RULE
			]);
	}
}