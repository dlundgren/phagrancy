<?php

/**
 * @file
 * Contains Phagrancy\Action\AllClear
 */

namespace Phagrancy\Action;
use Phagrancy\Http\Response\Json;

/**
 * Sends a generic 200 Response
 *
 * @package Phagrancy\Action
 */
class AllClear
{
	public function __invoke()
	{
		return new Json();
	}
}