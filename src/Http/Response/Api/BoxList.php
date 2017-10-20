<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Api\BoxList
 */

namespace Phagrancy\Http\Response\Api;

use Phagrancy\Http\Response\RespondsWithJson;
use Phagrancy\Model\Entity;
use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * Box JSON list response
 *
 * @package Phagrancy\Http\Response\Api
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