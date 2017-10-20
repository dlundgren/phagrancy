<?php

namespace Phagrancy\Model\Input;

use Validator\LIVR;

trait IsValidator
{
	private function perform($data = [], $rules = [])
	{
		return (new LIVR($rules))->validate($data);
	}
}