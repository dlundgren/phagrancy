<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\SendFile
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Stream;

/**
 * Action for sending a file to the requester
 *
 * @package Phagrancy\Action\Api\Scope\Box
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
	private $validator;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $validator, $uploadPath, $useNginxAccelRedirect)
	{
		$this->boxes                 = $boxes;
		$this->validator             = $validator;
		$this->uploadPath            = $uploadPath;
		$this->useNginxAccelRedirect = $useNginxAccelRedirect;
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
		$params = $this->validator->validate($request->getAttribute('route')->getArguments());
		extract($params);
		$box = $this->boxes->ofNameInScope($name, $scope);
		$response = new Response\NotFound("Box not found: {$box->path()}");
		$path = "/{$box->path()}/{$version}/{$provider}.box";

		if ($box && file_exists("{$this->uploadPath}{$path}")) {
			if (file_exists)
			$response = new \Slim\Http\Response();
			$response = $response->header('Cache-control', 'must-revalidate');
			$response = $response->header('Expires', 0);
			$response = $response->header('Content-Type', 'application/octet-stream');
			$response = $response->header('Content-Disposition', 'attachment; filename="' . "{$box->name()}-{$provider}-{$version}.box" . '"');

//			if ($this->useNginxAccelRedirect) {
//				$response = $response->header('X-Accel-Redirect', $path);
//			}
//			else {
				$response = $response->withBody(new Stream(fopen("{$this->uploadPath}{$path}", 'rb')));
//			}
		}

		return $response;
	}
}