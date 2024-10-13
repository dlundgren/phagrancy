<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadPreFlight
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for handling the Upload confirmation callback
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class UploadConfirm
	extends UploadAction
{
	/**
	 * @param ServerRequestInterface $request
	 * @return Response\Json|Response\NotFound
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$response = $this->validate($request);
		if ($response instanceof Response\Json) {
			// box already exists...
			return new Response\AllClear();
		}
		else {
			return new Response\Json(['errors' => ['not uploaded']], 409);
		}
	}
}