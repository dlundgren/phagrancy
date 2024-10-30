<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box\Delete
 */

namespace Phagrancy\Action\Api\Scope\Box;

use Phagrancy\Concern\FindsBox;
use Phagrancy\Http\Response;
use Phagrancy\Model\Input;
use Phagrancy\Model\Repository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action for deleting a box from the server
 *
 * @package Phagrancy\Action\Api\Scope\Box
 */
class Delete
{
	use FindsBox;

	/**
	 * @var Repository\Box
	 */
	private $boxes;

	/**
	 * @var string
	 */
	private $storagePath;

	/**
	 * @var Input\BoxDelete
	 */
	private $input;

	public function __construct(Repository\Box $boxes, Input\BoxDelete $input, $storagePath)
	{
		$this->boxes       = $boxes;
		$this->input       = $input;
		$this->storagePath = $storagePath;
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

		$boxPath = $this->findBox($params, $this->storagePath);
		if ($boxPath) {
			if (is_writable($boxPath) && unlink($boxPath)) {
				return new Response\AllClear();
			}
			else {
				return new Response\Json(['errors' => 'unable to delete'], 409);
			}
		}

		return new Response\NotFound();
	}
}
