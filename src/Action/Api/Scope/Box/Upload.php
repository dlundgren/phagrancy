<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Upload
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for uploading a box to the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Upload
	extends UploadAction
{
	public function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		$this->storage->saveFromRequest(
			$request,
			$this->params->publicPath()
		);

		return new Response\AllClear();
	}
}