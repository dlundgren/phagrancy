<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\Api\BoxDefinition
 */

namespace Phagrancy\Http\Response\Api;

use Phagrancy\Http\Response\RespondsWithJson;
use Phagrancy\Model\Entity;
use Slim\Http\Response;

/**
 * Box JSON definition response
 *
 * @package Phagrancy\Http\Response\Api
 */
class BoxDefinition
	extends Response
{
	use RespondsWithJson;

	public function __construct(Entity\Box $box, $uri)
	{
		$json = [
			'tag'      => $box->path(),
			'versions' => []
		];
		foreach ($box->versions() as $version => $providers) {
			if (empty($providers)) {
				continue;
			}
			$vpbs = [];
			foreach ($providers as $provider) {
				$vpbs[] = [
					'name' => $provider,
					'url'  => (string)$uri->withPath("/api/v1/box/{$box->path()}/{$version}/{$provider}")
				];
			}
			$json['versions'][] = [
				'version'   => $version,
				'providers' => $vpbs
			];
		}
		$this->constructResponse($json);
	}
}