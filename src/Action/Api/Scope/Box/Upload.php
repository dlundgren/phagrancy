<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Upload
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Http\Response;
use Phagrancy\Model\Entity\Box;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for uploading a box to the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Upload
	extends UploadAction
{
	public function perform(ServerRequestInterface $request, Box $box, $params): ResponseInterface
	{
		extract($params);

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
		if (!file_exists($path = "{$this->uploadPath}/{$box->path()}/{$version}/")) {
			mkdir($path, 0755, true);
		}

		// the box name is now {provider}-{architecture}.box, if there is no architecture, then we don't worry
		$architecture = $architecture ?? 'unknown';
		$boxPath = "$path/{$provider}-{$architecture}.box";

		rename($tmp, $boxPath);

		return new Response\AllClear();
	}
}