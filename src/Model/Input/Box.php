<?php

namespace Phagrancy\Model\Input;

use Validator\LIVR;

class Box
{
	use IsValidator, ValidatesScope, ValidatesBoxName;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope' => [$this, 'validateScope'],
				'name'  => [$this, 'validateBoxName'],
			]);
	}

	public function validate($params)
	{
		return $this->perform(
			$params,
			[
				'scope' => self::$SCOPE_RULE,
				'name'  => self::$BOX_NAME_RULE
			]);
	}
}