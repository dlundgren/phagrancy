<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\SendFile
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Concern\FindsBox;
use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Service\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for sending a file to the requester
 *
 * @package Phagrancy\Action\Scope\Box
 */
class SendFile
{
	use FindsBox;

	private Repository\Box $boxes;

	private Storage $storage;

	private Input\BoxUpload $input;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, Storage $storage)
	{
		$this->boxes   = $boxes;
		$this->input   = $input;
		$this->storage = $storage;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$vagrant = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($vagrant->isValid()) {
			$box     = $this->boxes->ofNameInScope($vagrant->name, $vagrant->scope);
			$boxPath = $this->findBox($vagrant, $this->storage);
			if (!empty($box) && !empty($boxPath)) {
				return new Response\SendBoxFile($box, $vagrant->version, $vagrant->provider, $boxPath);
			}
		}

		return new Response\NotFound();
	}
}