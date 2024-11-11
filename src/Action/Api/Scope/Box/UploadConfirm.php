<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadConfirm
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Psr\Http\Message\ResponseInterface;
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
	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$response = $this->validate($request);

		// reverse the logic as an error means the box was uploaded
		return $response instanceof Response\Error
			? new Response\AllClear()
			: new Response\Error('not uploaded');
	}
}