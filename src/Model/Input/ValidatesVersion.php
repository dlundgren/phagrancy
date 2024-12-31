<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\ValidatesVersion
 */

namespace Phagrancy\Model\Input;

/**
 * Version Validator
 *
 * This validator attempts to align itself with vagrant-clouds SemVer style.
 *
 * @package Phagrancy\Model\Input
 */
trait ValidatesVersion
{
	private static array $VERSION_RULE = ['required', 'trim', 'version'];

	public function validateVersion(): \Closure
	{
		$error = "Version must be of the format x.y.z where x, y, and z are all integers";
		return function ($value) use ($error) {
			// we support a subset of SemVer
			if (!preg_match('/^(\d+)(?:\.(\d+)){0,2}(?:-[\da-z]+)*$/', $value)) {
				return $error;
			}

			$pips = explode('.', $value);
			foreach ($pips as $pip) {
				if (is_numeric($pip) && !((int)$pip >= 0)) {
					return $error;
				}
			}
		};
	}
}