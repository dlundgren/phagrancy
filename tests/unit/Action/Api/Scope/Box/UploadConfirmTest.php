<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\UploadConfirmTest
 */

namespace unit\Action\Api\Scope\Box;

use Phagrancy\Action\Api\Scope\Box\UploadConfirm;
use Phagrancy\Http\Response\AllClear;
use Phagrancy\Model\Input\BoxUpload;
use Phagrancy\Model\Repository\Box;
use Phagrancy\TestCase\Scope as ScopeTestCase;

class UploadConfirmTest
	extends ScopeTestCase
{
	private function confirmBox(string $version = '1.0')
	{
		$request = $this->buildRequest();
		$request->getAttribute('route')
		        ->setArguments(
			        [
				        'scope'        => 'test',
				        'name'         => 'upload',
				        'version'      => $version,
				        'provider'     => 'test',
				        'architecture' => 'amd64'
			        ]
		        );

		// build the action itself
		$action   = new UploadConfirm(
			new Box($this->storage),
			new BoxUpload(),
			$this->storage
		);

		return $action($request);
	}

	public function testReturnsOkForExistingBox()
	{
		$this->storage->write("test/upload/1.0/test-amd64.box", 'kakaw');

		self::assertInstanceOf(AllClear::class, $this->confirmBox());
	}

	public function testReturnsErrorForNotUploadedBox()
	{
		$response = $this->confirmBox();

		self::assertResponseHasStatus($response, 409);
		self::assertMessageBodyEqualsJsonArray($response, ["errors" => ["not uploaded"]]);
	}
}