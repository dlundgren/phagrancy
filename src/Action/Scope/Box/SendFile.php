<?php

/**
 * @file
 * Contains Phagrancy\Action\Scope\Box\SendFile
 */

namespace Phagrancy\Action\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity;
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
	private $validator;

	public function __construct(Repository\Box $boxes, Input\BoxUpload $validator, $uploadPath)
	{
		$this->boxes                 = $boxes;
		$this->validator             = $validator;
		$this->uploadPath            = $uploadPath;
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
		$box      = $this->boxes->ofNameInScope($name, $scope);
		$path     = "/{$box->path()}/{$version}/{$provider}.box";

		if ($box && file_exists("{$this->uploadPath}{$path}")) {
			$response = new \Slim\Http\Response(
				200,
				new Headers(
					[
						'Cache-Control'       => "must-revalidate",
						'Expires'             => 0,
						'Content-Type'        => 'application/octet-stream',
						'Content-Disposition' => 'attachment; filename="' . "{$box->name()}-{$provider}-{$version}.box" . '"'
					]),
				new Stream(fopen("{$this->uploadPath}{$path}", 'rb'))
			);
		}
		else {
			$response = new Response\NotFound();
		}

		return $response;
	}
}