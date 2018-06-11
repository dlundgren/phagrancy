<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\IsValidator
 */

namespace Phagrancy\Model\Input;

use Validator\LIVR;

/**
 * Trait IsValidator
 *
 * @package Phagrancy\Model\Input
 */
trait IsValidator
{
	private function perform($data = [], $rules = [])
	{
		return (new LIVR($rules))->validate($data);
	}
}