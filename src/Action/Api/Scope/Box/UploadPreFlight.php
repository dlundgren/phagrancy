<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadPreFlight
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for handling the upload pre-flight sequence
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class UploadPreFlight
	extends UploadAction
{
	use ReturnsUrlForBox;

	protected function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		return new Response\Json(
			[
				'upload_path' => (string)$request->getUri()->withPath("{$this->params->apiPath()}/upload")
			]
		);
	}
}