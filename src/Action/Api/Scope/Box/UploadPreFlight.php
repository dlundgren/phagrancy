<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadPreFlight
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;

/**
 * Action for handling the upload pre-flight sequence
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class UploadPreFlight
{
	use ReturnsUrlForBox;

	public function __invoke(ServerRequestInterface $request)
	{
		$path = $this->createUrlFromRouteParams($request->getAttribute('route')->getArguments());
		$uri    = $request->getUri();
		$r      = new \Slim\Http\Response();
		$json = [
			'upload_path' => (string)$uri->withPath("{$path}/upload")
		];

		$r->getBody()->write(json_encode($json));

		return $r;
	}
}