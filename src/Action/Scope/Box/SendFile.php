<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\SendFile
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Headers;
use Slim\Http\Stream;

/**
 * Action for sending a file to the requester
 *
 * @package Phagrancy\Action\Scope\Box
 */
class SendFile
{
	/**
	 * @var Repository\Box
	 */
	private $boxes;

	/**
	 * @var string
	 */
	private $uploadPath;

	/**
	 * @var Input\BoxUpload
	 */
	private $input;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $input, $uploadPath)
	{
		$this->boxes      = $boxes;
		$this->input      = $input;
		$this->uploadPath = $uploadPath;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		/**
		 * The route controls these params, and they are validated so safe
		 *
		 * @var string $name
		 * @var string $scope
		 * @var string $version
		 * @var string $provider
		 */
		$params = $this->input->validate($request->getAttribute('route')->getArguments());
		if (!$params) {
			return new Response\NotFound();
		}

		extract($params);
		$box  = $this->boxes->ofNameInScope($name, $scope);
		$file = "{$this->uploadPath}/{$box->path()}/{$version}/{$provider}.box";

		return ($box && file_exists($file))
			? new Response\SendBoxFile($box, $version, $provider, $file)
			: new Response\NotFound();
	}
}