<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\BoxUpload
 */

namespace Phagrancy\Model\Input;

use Validator\LIVR;

/**
 * Input for the BoxUpload
 *
 * Validates the scope, name, version, provider
 *
 * @package Phagrancy\Model\Input
 */
class BoxUpload
{
	use IsValidator, ValidatesScope, ValidatesBoxName;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope' => [$this, 'validateScope'],
				'name'  => [$this, 'validateBoxName'],
				'version' => [$this, 'validateVersion'],
				'provider' => [$this,'validateProvider']
			]);
	}

	public function validate($params)
	{
		return $this->perform(
			$params,
			[
				'scope' => self::$SCOPE_RULE,
				'name'  => self::$BOX_NAME_RULE,
				'version' => ['required', 'trim', 'to_lc', 'version'],
				'provider' => ['required', 'trim', 'to_lc']
			]);
	}

	public function validateVersion()
	{
		return function ($value) {
			if (!preg_match('/^[0-9]([0-9\.]+)?$/i', $value)) {
				return 'must be semantically versioned';
			}
		};
	}
}