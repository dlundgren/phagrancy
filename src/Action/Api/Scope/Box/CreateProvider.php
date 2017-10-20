<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateProvider
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;

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
		$r    = new \Slim\Http\Response();
		$data = $request->getParsedBody();
		$json = $data['provider'];

		// @TODO pass this through the validator
		$params             = $request->getAttribute('route')->getArguments();
		$params['provider'] = $json['name'];
		$path               = $this->createUrlFromRouteParams($params);

		// specify the upload url path;
		$json['upload_url'] = (string)$uri->withPath("{$path}/upload");

		$r->getBody()->write(json_encode($json));

		return $r;
	}
}