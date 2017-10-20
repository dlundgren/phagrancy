<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Definition
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for uploading a box to the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Upload
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
		$this->boxes      = $boxes;
		$this->validator  = $validator;
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
		$params = $this->validator->validate($request->getAttribute('route')->getArguments());
		extract($params);
		$box = $this->boxes->ofNameInScope($name, $scope);
		if ($box) {
			// we need to upload the file
			$request->getBody()->detach();
			$from = fopen("php://input", 'r');

			mkdir("{$this->uploadPath}/tmp", 0755, true);
			$tmp = tempnam("{$this->uploadPath}/tmp", 'phagrancy');
			$to  = fopen($tmp, 'w');
			stream_copy_to_stream($from, $to);
			fclose($from);
			fclose($to);

			// make sure it exists
			$path = "{$this->uploadPath}/{$box->path()}/{$version}/";
			mkdir($path, 0755, true);

			rename($tmp, "$path/{$provider}.box");
		}

		return new \Slim\Http\Response();
	}
}