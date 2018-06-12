<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\CreateVersion
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input\BoxVersion;
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
	/**
	 * @var BoxVersion
	 */
	private $input;

	public function __construct(BoxVersion $input)
	{
		$this->input = $input;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$data = $request->getParsedBody();
		if (empty($data)) {
			return new Response\InvalidRequest(['version' => 'Version is required']);
		}

		// merge the data in
		$args   = $request->getAttribute('route')->getArguments();
		$params = $this->input->validate(array_merge($data['version'], $args));
		if (!$params) {
			return new Response\InvalidRequest($this->input->errors());
		}

		return new Response\Json($data['version']);
	}
}