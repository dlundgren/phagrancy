<?php

/**
 * @file
 * Contains Phagrancy\Action\AllClear
 */

namespace Phagrancy\Action;

use Phagrancy\Http\Response\AllClear as AllClearResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Sends a generic 200 Response
 *
 * @package Phagrancy\Action
 */
class AllClear
{
	public function __invoke(): ResponseInterface
	{
		return new AllClearResponse;
	}
}