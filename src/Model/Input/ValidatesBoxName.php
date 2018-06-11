<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\ValidatesBoxName
 */

namespace Phagrancy\Model\Input;

/**
 * Validator for the box names
 *
 * @package Phagrancy\Model\Input
 */
trait ValidatesBoxName
{
	/**
	 * @var array Generic list of rules
	 */
	private static $BOX_NAME_RULE = ['required', 'trim', 'to_lc', 'name'];

	/**
	 * Validates the box name
	 *
	 * @return \Closure
	 */
	public function validateBoxName()
	{
		return function ($value) {
			if (!preg_match('/^[a-z0-9-]+$/i', $value)) {
				return 'only letters, numbers, and dashes are allowed';
			}
		};
	}
}