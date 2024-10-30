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
 * Validates the scope, name, version, provider, architecture
 *
 * @package Phagrancy\Model\Input
 */
class BoxUpload
{
	use IsValidator, ValidatesScope, ValidatesBoxName, ValidatesVersion;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope'        => [$this, 'validateScope'],
				'name'         => [$this, 'validateBoxName'],
				'version'      => [$this, 'validateVersion'],
				'provider'     => [$this, 'validateProvider'],
				'architecture' => [$this, 'validateArchitecture'],
			]);
	}

	public function validate($params)
	{
		return $this->perform(
			$params,
			[
				'scope'        => self::$SCOPE_RULE,
				'name'         => self::$BOX_NAME_RULE,
				'version'      => self::$VERSION_RULE,
				'provider'     => ['required', 'trim', 'to_lc'],
				'architecture' => ['trim', 'to_lc']
			]);
	}
}