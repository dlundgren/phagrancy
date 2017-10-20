<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\BoxList
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Model\Entity;
use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * Box JSON list response
 *
 * @package Phagrancy\Http\Response
 */
class BoxList
	extends Response
{
	use RespondsWithJson;

	public function __construct(Entity\Scope $scope)
	{
		$this->constructResponse(
			[
				'username' => $scope->name(),
				'boxes'    => $scope->boxes()
			]);
	}
}