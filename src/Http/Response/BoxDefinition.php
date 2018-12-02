<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\BoxDefinition
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Model\Entity;
use Psr\Http\Message\UriInterface;

/**
 * Box JSON definition response
 *
 * @package Phagrancy\Http\Response
 */
class BoxDefinition
	extends Json
{
	public function __construct(Entity\Box $box, UriInterface $uri)
	{
		$json = [
			'name'      => (string)$box->path(),
			'versions' => []
		];
		foreach ($box->versions() as $version => $providers) {
			if (empty($providers)) {
				continue;
			}
			$vpbs = [];
			foreach ($providers as $provider) {
				$vpbs[] = [
					'name' => (string)$provider,
					'url'  => (string)$uri->withPath("/{$box->path()}/{$version}/{$provider}")
				];
			}
			$json['versions'][] = [
				'version'   => strval($version),
				'providers' => $vpbs
			];
		}

		parent::__construct($json);
	}
}
