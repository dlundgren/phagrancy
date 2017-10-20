<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\Definition
 */

namespace Phagrancy\Action\Scope\Box;

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

		return '/api/v1/box/' . join('/', $url);
	}
}