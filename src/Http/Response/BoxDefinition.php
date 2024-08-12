<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\BoxDefinition
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Concern\GeneratesDefinition;
use Phagrancy\Model\Entity;

/**
 * Box JSON definition response
 *
 * @package Phagrancy\Http\Response
 */
class BoxDefinition
	extends Json
{
	use GeneratesDefinition;

	/**
	 * @var string The key name for the definition. API uses tag, non-API uses name
	 */
	protected $nameKey = 'name';

	protected function resolveUriPath(Entity\Box $box, $version, $provider)
	{
		return "/{$box->path()}/{$version}/{$provider}";
	}
}
