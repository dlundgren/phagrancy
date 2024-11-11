<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Delete
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Concern\FindsBox;
use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Service\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for deleting a box from the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Delete
{
	use FindsBox;

	private Repository\Box $boxes;

	private Input\BoxDelete $input;

	private Storage $storage;

	public function __construct(Repository\Box $boxes, Input\BoxDelete $input, Storage $storage)
	{
		$this->boxes   = $boxes;
		$this->input   = $input;
		$this->storage = $storage;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$input = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($input->isValid()) {
			$boxPath = $this->findBox($input, $this->storage);
			if ($boxPath) {
				if ($this->storage->delete($boxPath)) {
					return new Response\AllClear;
				}
				else {
					return new Response\Json(['errors' => 'unable to delete'], 409);
				}
			}
		}

		return new Response\NotFound();
	}
}
