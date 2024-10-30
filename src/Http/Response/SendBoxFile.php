<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\SendFile
 */

namespace Phagrancy\Http\Response;

use Phagrancy\Model\Entity\Box;
use Slim\Http\Headers;
use Slim\Http\Response;
use Slim\Http\Stream;

/**
 * Sends the box file
 *
 * @package Phagrancy\Http\Response
 */
class SendBoxFile
	extends Response
{
	public function __construct(Box $box, string $version, string $provider, string $file)
	{
		$filename = "{$box->name()}-{$provider}-{$version}.box";
		parent::__construct(
			200,
			new Headers(
				[
					'Cache-Control'       => "must-revalidate",
					'Expires'             => 0,
					'Content-Type'        => 'application/octet-stream',
					'Content-Disposition' => 'attachment; filename="' . $filename . '"'
				]
			),
			new Stream(fopen($file, 'rb'))
		);
	}
}