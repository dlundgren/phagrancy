<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\IsValidator
 */

namespace Phagrancy\Model\Input;

use Phagrancy\Http\Context\Vagrant;
use Psr\Http\Message\ServerRequestInterface;
use Validator\LIVR;

/**
 * Trait IsValidator
 *
 * @package Phagrancy\Model\Input
 */
trait IsValidator
{
	protected array $errors;

	protected array $validation = [];

	public function errors(): array
	{
		return $this->errors;
	}

	public function validateFromRequest(ServerRequestInterface $request): Vagrant
	{
		return $this->perform(
			array_merge(
				$request->getParsedBody() ?? [],
				$request->getAttribute('route')->getArguments()
			),
			$this->validation
		);
	}

	private function perform(array $data = [], array $rules = []): Vagrant
	{
		$validator = new LIVR($rules);

		return Vagrant::createFromInput(
			$validator->validate($data),
			$validator->getErrors()
		);
	}

	public function validate($params): Vagrant
	{
		return $this->perform($params, $this->validation);
	}
}