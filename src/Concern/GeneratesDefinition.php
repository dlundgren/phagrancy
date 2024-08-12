<?php

/**
 * @file
 * Contains Phagrancy\Concern\GeneratesDefinition
 */

namespace Phagrancy\Concern;

use Phagrancy\Model\Entity;
use Psr\Http\Message\UriInterface;

/**
 * Box JSON definition generator
 *
 * @package Phagrancy\Concern
 */
trait GeneratesDefinition
{
	public function __construct(Entity\Box $box, UriInterface $uri)
	{
		parent::__construct($this->generateDefinition($box, $uri));
	}

	public function generateDefinition(Entity\Box $box, UriInterface $uri)
	{
		$json = [
			$this->nameKey => $box->path(),
			'versions'     => []
		];

		foreach ($box->versions() as $version => $providers) {
			if (empty($providers)) {
				continue;
			}
			$versionedProviders = [];
			foreach ($providers as $provider) {
				$versionedProviders[] = [
					'name' => (string)$provider,
					'url'  => (string)$uri->withPath($this->resolveUriPath($box, $version, $provider))
				];
			}
			$json['versions'][] = [
				'version'   => (string)$version,
				'providers' => $versionedProviders
			];
		}

		return $json;
	}
}