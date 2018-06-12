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
	/**
	 * @var array list of errors
	 */
	protected $errors;

	/**
	 * @return array The list of errors
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Performs the LIVR validation
	 *
	 * @param array $data
	 * @param array $rules
	 * @return mixed
	 */
	private function perform($data = [], $rules = [])
	{
		$validator = new LIVR($rules);
		$valid     = $validator->validate($data);
		if (!$valid) {
			$this->errors = $validator->getErrors();
		}

		return $valid;
	}
}