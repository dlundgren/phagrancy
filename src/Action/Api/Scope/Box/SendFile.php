<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\SendFile
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
 * Action for sending a file to the requester
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class SendFile
{
	use FindsBox;

	private Repository\Box $boxes;

	private Input\BoxUpload $input;

	private Storage $storage;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, Storage $storage)
	{
		$this->boxes   = $boxes;
		$this->input   = $input;
		$this->storage = $storage;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$input = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($input->isValid()) {
			$box     = $this->boxes->ofNameInScope($input->name, $input->scope);
			$boxPath = $this->findBox($input, $this->storage);
		}

		return (isset($box) && isset($boxPath))
			? new Response\SendBoxFile($box, $input->version, $input->provider, $boxPath)
			: new Response\NotFound();
	}
}