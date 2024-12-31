<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\ValidatesScope
 */

namespace Phagrancy\Model\Input;

/**
 * Scope Validator
 *
 * @package Phagrancy\Model\Input
 */
trait ValidatesScope
{
	private static array $SCOPE_RULE = ['required', 'trim', 'to_lc', 'scope'];

	/**
	 * What constitutes a valid username?
	 * - at least 3 chars
	 * - letters, numbers, dashes (& underscores?)
	 */
	public function validateScope(): \Closure
	{
		return function($value) {
			if (mb_strlen($value) < 3) {
				return "must be at least 3 characters";
			}

			if (!preg_match('/^[a-z0-9-]+$/i', $value)) {
				return 'only letters, numbers, and dashes are allowed';
			}
		};
	}
}