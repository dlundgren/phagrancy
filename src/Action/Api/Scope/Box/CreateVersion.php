<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateVersion
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input\BoxVersion;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action to create a box version
 *
 * This just responds with the version information passed in to it
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class CreateVersion
{
	private BoxVersion $input;

	public function __construct(BoxVersion $input)
	{
		$this->input = $input;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$input = $this->input->validateFromRequest($request);

		return $input->isValid()
			? new Response\Json($input->body['version'])
			: new Response\InvalidRequest($input->errors);
	}
}