<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadAction
 */


namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Concern\FindsBox;
use Phagrancy\Http\Context\Vagrant;
use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Phagrancy\Service\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class UploadAction
{
	use FindsBox;

	protected Repository\Box $boxes;

	protected Input\BoxUpload $input;

	protected Vagrant $params;

	protected Storage $storage;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, Storage $storage)
	{
		$this->boxes   = $boxes;
		$this->input   = $input;
		$this->storage = $storage;
	}

	public function __invoke(ServerRequestInterface $request): ResponseInterface
	{
		$box = $this->validate($request);

		return $box instanceof Response\Json
			? $box
			: $this->perform($request, $box, $this->params);
	}

	/**
	 * Not abstract as not all child classes use this method
	 *
	 * @param ServerRequestInterface $request
	 * @param Box $box
	 * @param $params
	 * @return ResponseInterface
	 */
	protected function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		return new Response\AllClear();
	}

	/**
	 * Validates the request
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface|Box
	 */
	protected function validate(ServerRequestInterface $request)
	{
		if (!$this->storage->isAvailable()) {
			return new Response\InternalServerError("Unable to write to disk");
		}

		$this->params = $this->input->validate($request->getAttribute('route')->getArguments());
		if ($this->params->isValid()) {
			$box     = $this->boxes->ofNameInScope($this->params->name, $this->params->scope);
			$boxPath = $this->findBox($this->params, $this->storage);
		}
		else {
			return new Response\NotFound();
		}

		if (isset($boxPath)) {
			return new Response\Error("box already exists: {$this->params->errorPath()}");
		}

		return $box ?? new Response\AllClear();
	}
}