<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadDirect
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Service\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for handling the upload pre-flight sequence
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class UploadDirect
	extends UploadAction
{
	protected ?string $token;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, Storage $storage, ?string $token)
	{
		parent::__construct($boxes, $input, $storage);
		$this->token = $token;
	}

	protected function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		$path       = $this->params->apiPath();
		$signed     = hash_hmac('sha256', "PUT\n{$path}/upload", $this->token);
		$upload_url = $request->getUri()->withPath("{$path}/upload")->withQuery("X-Phagrancy-Signature={$signed}");

		return new Response\Json(
			[
				'upload_path' => (string)$upload_url,
				'callback'    => (string)$request->getUri()->withPath("{$path}/upload/confirm")
			]
		);
	}
}