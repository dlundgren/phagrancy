<?php

namespace Phagrancy\Model\Input;

trait ValidatesBoxName
{
	private static $BOX_NAME_RULE = ['required', 'trim', 'to_lc', 'name'];
	public function validateBoxName()
	{
		return function ($value) {
			if (!preg_match('/^[a-z0-9-]+$/i', $value)) {
				return 'only letters, numbers, and dashes are allowed';
			}
		};
	}
}