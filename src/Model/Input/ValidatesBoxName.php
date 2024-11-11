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
	private static array $BOX_NAME_RULE = ['required', 'trim', 'to_lc', 'name'];

	public function validateBoxName(): \Closure
	{
		return function ($value) {
			if (!preg_match('/^[a-z0-9-]+$/i', $value)) {
				return 'only letters, numbers, and dashes are allowed';
			}
		};
	}
}