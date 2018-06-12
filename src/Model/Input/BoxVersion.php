<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\BoxVersion
 */

namespace Phagrancy\Model\Input;

use Validator\LIVR;

/**
 * Input for the BoxVersion
 *
 * Validates the scope, name, version
 *
 * @package Phagrancy\Model\Input
 */
class BoxVersion
{
	use IsValidator, ValidatesScope, ValidatesBoxName, ValidatesVersion;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope'    => [$this, 'validateScope'],
				'name'     => [$this, 'validateBoxName'],
				'version'  => [$this, 'validateVersion'],
			]);
	}

	public function validate($params)
	{
		return $this->perform(
			$params,
			[
				'scope'    => self::$SCOPE_RULE,
				'name'     => self::$BOX_NAME_RULE,
				'version'  => self::$VERSION_RULE,
			]);
	}
}