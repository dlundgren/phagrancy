<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadPreFlight
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

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
		$json = [
			'upload_path' => (string)$request->getUri()->withPath("{$path}/upload")
		];

		return new Response\Json($json);
	}
}