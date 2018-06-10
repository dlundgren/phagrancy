<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Upload
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
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
		extract($params);
		$box = $this->boxes->ofNameInScope($name, $scope);
		if ($box) {
			if (!file_exists("{$this->uploadPath}/tmp")) {
				mkdir("{$this->uploadPath}/tmp", 0755, true);
			}
			$tmp = tempnam("{$this->uploadPath}/tmp", 'phagrancy');

			$request->getBody()->detach();
			$from = fopen("php://input", 'r');
			$to   = fopen($tmp, 'w');

			stream_copy_to_stream($from, $to);
			fclose($from);
			fclose($to);

			// make sure it exists
			$path = "{$this->uploadPath}/{$box->path()}/{$version}/";
			if (!file_exists($path)) {
				mkdir($path, 0755, true);
			}

			rename($tmp, "$path/{$provider}.box");
		}

		return new Response\Json([]);
	}
}