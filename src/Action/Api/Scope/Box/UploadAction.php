<?php

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class UploadAction
{
	/**
	 * @var Repository\Box
	 */
	protected $boxes;

	/**
	 * @var Input\BoxUpload
	 */
	protected $input;

	/**
	 * @var array List of validated parameters
	 */
	protected $params = [];

	/**
	 * @var string
	 */
	protected $uploadPath;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, $uploadPath)
	{
		$this->boxes      = $boxes;
		$this->input      = $input;
		$this->uploadPath = $uploadPath;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$box = $this->validate($request);

		return $box instanceof Response\Json
			? $box
			: $this->perform($request, $box, $this->params);
	}

	/**
	 * Not abstract as not all child classes use this method
	 *
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
		if (!is_writable($this->uploadPath)) {
			if (!is_writable(dirname($this->uploadPath))) {
				return new Response\InternalServerError("Unable to write to disk: {$this->uploadPath}");
			}
			else {
				mkdir($this->uploadPath);
			}
		}

		/**
		 * The route controls these params, and they are validated so safe
		 *
		 * @var string $name
		 * @var string $scope
		 * @var string $version
		 * @var string $provider
		 */
		$this->params = $this->input->validate($request->getAttribute('route')->getArguments());
		if (!$this->params) {
			return new Response\NotFound();
		}

		extract($this->params);
		$box = $this->boxes->ofNameInScope($name, $scope);
		$path = "{$this->uploadPath}/{$box->path()}/{$version}/";

		// the box name is now {provider}-{architecture}.box, if there is no architecture, then we don't worry
		$architecture = $architecture ?? 'unknown';
		$boxPath = "$path/{$provider}-{$architecture}.box";

		// If box with same version and provider already exists prevent overwriting
		if (file_exists($boxPath)) {
			return new Response\Json(['errors'=>["box already exists: {$box->path()}/$provider/$architecture"]], 409);
		}

		return $box ?? new Response\AllClear();
	}
}