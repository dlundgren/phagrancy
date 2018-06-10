<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Api\BoxList
 */

namespace Phagrancy\Http\Response\Api;

use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Entity;

/**
 * Box JSON list response
 *
 * @package Phagrancy\Http\Response\Api
 */
class BoxList
	extends Json
{
	public function __construct(Entity\Scope $scope)
	{
		parent::__construct(
			[
				'username' => $scope->name(),
				'boxes'    => $scope->boxes()
			]);
	}
}