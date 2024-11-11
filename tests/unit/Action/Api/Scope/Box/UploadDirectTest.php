<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadDirectTest
 */

namespace unit\Action\Api\Scope\Box;

use Phagrancy\Action\Api\Scope\Box\UploadDirect;
use Phagrancy\Http\Response\Json;
use Phagrancy\Model\Input\BoxUpload;
use Phagrancy\Model\Repository\Box;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class UploadDirectTest
	extends ScopeTestCase
{
	private function uploadBox(string $content)
	{
		$request = $this->buildRequest();
		$request->getAttribute('route')
		        ->setArguments(
			        [
				        'scope'        => 'test',
				        'name'         => 'upload',
				        'version'      => '1.0',
				        'provider'     => 'test',
				        'architecture' => 'amd64'
			        ]
		        );

		// build the action itself
		$action   = new UploadDirect(
			new Box($this->storage),
			new BoxUpload(),
			$this->storage,
			'kakaw'
		);

		return $action($request);
	}

	public function testReturnsOkForExistingBox()
	{
		$path   = "api/v1/box/test/upload/version/1.0/provider/test/amd64";
		$signed = hash_hmac('sha256', "PUT\n/{$path}/upload", 'kakaw');

		self::assertMessageBodyEqualsJsonArray(
			$this->uploadBox('uploading'),
			[
				'upload_path' => "http://localhost/{$path}/upload?X-Phagrancy-Signature={$signed}",
				'callback'    => "http://localhost/{$path}/upload/confirm"
			]
		);
	}

	public function testReturnsErrorForAlreadyUploadedBox()
	{
		$this->storage->write(
			'test/upload/1.0/test-amd64.box',
			'already here'
		);

		$response = $this->uploadBox('override box');

		self::assertResponseHasStatus($response, 409);
		self::assertMessageBodyEqualsJsonArray(
			$response,
			[
				"errors" => [
					"box already exists: test/upload/1.0/test/amd64"
				]
			]
		);
	}
}