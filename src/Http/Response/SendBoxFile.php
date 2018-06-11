<?php

/**
 * @file
 * Contains Phagrancy\Http\Response\SendFile
 */

namespace Phagrancy\Http\Response;

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
	public function __construct($box, $version, $provider, $file)
	{
		parent::__construct(
			200,
			new Headers(
				[
					'Cache-Control'       => "must-revalidate",
					'Expires'             => 0,
					'Content-Type'        => 'application/octet-stream',
					'Content-Disposition' => 'attachment; filename="' . "{$box->name()}-{$provider}-{$version}.box" . '"'
				]),
			new Stream(fopen($file, 'rb'))
		);
	}
}