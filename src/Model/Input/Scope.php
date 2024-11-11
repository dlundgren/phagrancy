<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\Scope
 */

namespace Phagrancy\Model\Input;

use Phagrancy\Http\Context\Vagrant;
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

		$this->validation = [
			'scope' => static::$SCOPE_RULE
		];
	}

	public function validate($params): Vagrant
	{
		return $this->perform($params, $this->validation);
	}
}