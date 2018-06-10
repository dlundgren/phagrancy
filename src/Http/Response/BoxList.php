<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\BoxList
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Model\Entity;

/**
 * Box JSON list response
 *
 * @package Phagrancy\Http\Response
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