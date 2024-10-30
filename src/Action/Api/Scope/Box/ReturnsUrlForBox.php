<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\ReturnsUrlForBox
 */

namespace Phagrancy\Action\Api\Scope\Box;

/**
 * Utility function for creating a url for a box
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
trait ReturnsUrlForBox
{
	protected function createUrlFromRouteParams($params)
	{
		// @TODO sanitize the input since we don't know if these have been validated
		$params = array_map(function ($v) {
			return str_replace('../', '', $v);
		}, $params);

		$url = [];
		isset($params['scope']) && $url[] = $params['scope'];
		isset($params['name']) && $url[] = $params['name'];
		isset($params['version']) && $url[] = "version/{$params['version']}";
		isset($params['provider']) && $url[] = "provider/{$params['provider']}";

		// should we validate the architecture is approved? i386,amd64,aarch64
		isset($params['architecture']) && $url[] = $params['architecture'];

		return '/api/v1/box/' . join('/', $url);
	}
}