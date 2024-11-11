<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Api\BoxDefinition
 */

namespace Phagrancy\Http\Response\Api;

use Phagrancy\Concern\GeneratesDefinition;
use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Entity;

/**
 * Box JSON definition response
 *
 * @package Phagrancy\Http\Response\Api
 */
class BoxDefinition
	extends Json
{
	use GeneratesDefinition;

	/**
	 * @var string The key name for the definition. API uses tag, non-API uses name
	 */
	protected string $nameKey = 'tag';

	protected function resolveUriPath(Entity\Box $box, string $version, string $provider): string
	{
		return "/api/v1/box/{$box->path()}/version/{$version}/provider/{$provider}";
	}
}