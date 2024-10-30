<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadPreFlight
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
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
	use ReturnsUrlForBox;

	/**
	 * @var string Token used for signing the URL
	 */
	protected $token;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, $uploadPath, $token)
	{
		parent::__construct($boxes, $input, $uploadPath);
		$this->token = $token;
	}

	protected function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		extract($params);

		$path   = $this->createUrlFromRouteParams($request->getAttribute('route')->getArguments());
		$signed = hash_hmac('sha256', "PUT\n$path/upload", $this->token);
		$json   = [
			'upload_path' => $request->getUri()->withPath("{$path}/upload") . "?X-Phagrancy-Signature={$signed}",
			'callback' => (string)$request->getUri()->withPath("{$path}/upload/confirm")
		];

		return new Response\Json($json);
	}
}