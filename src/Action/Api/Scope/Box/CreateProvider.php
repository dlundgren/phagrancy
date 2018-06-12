<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateProvider
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to create a provider
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class CreateProvider
{
	use ReturnsUrlForBox;

	public function __invoke(ServerRequestInterface $request)
	{
		$uri  = $request->getUri();
		$data = $request->getParsedBody();
		if (empty($data)) {
			return new Response\InvalidRequest(['provider' => 'Provider is required']);
		}

		$json = $data['provider'];

		// @TODO pass this through the validator
		$params             = $request->getAttribute('route')->getArguments();
		$params['provider'] = $json['name'];
		$path               = $this->createUrlFromRouteParams($params);

		// specify the upload url path;
		$json['upload_url'] = (string)$uri->withPath("{$path}/upload");

		return new Response\Json($json);
	}
}