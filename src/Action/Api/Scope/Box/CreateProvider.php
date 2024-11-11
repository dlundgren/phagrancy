<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateProvider
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input\BoxProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to create a provider
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class CreateProvider
{
	private BoxProvider $input;

	public function __construct(BoxProvider $input)
	{
		$this->input = $input;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$input = $this->input->validateFromRequest($request);

		return $input->isValid()
			? new Response\Json(
				[
					'name'       => $input->provider,
					'upload_url' => (string)$request->getUri()->withPath("{$input->apiPath()}/upload")
				]
			)
			: new Response\InvalidRequest($input->errors);
	}
}