<?php

/**
 * @file
 * Contains Phagrancy\Model\Input\BoxProvider
 */

namespace Phagrancy\Model\Input;

use Phagrancy\Http\Context\Vagrant;
use Psr\Http\Message\ServerRequestInterface;
use Validator\LIVR;

/**
 * Input for the BoxVersion
 *
 * Validates the scope, name, version
 *
 * @package Phagrancy\Model\Input
 */
class BoxProvider
	extends BoxVersion
{
	use IsValidator, ValidatesScope, ValidatesBoxName, ValidatesVersion;

	public function __construct()
	{
		LIVR::registerDefaultRules(
			[
				'scope'    => [$this, 'validateScope'],
				'name'     => [$this, 'validateBoxName'],
				'version'  => [$this, 'validateVersion']
			]
		);
		$this->validation = [
			'scope'    => self::$SCOPE_RULE,
			'name'     => self::$BOX_NAME_RULE,
			'version'  => self::$VERSION_RULE,
			'provider' => ['required', 'trim', 'to_lc']
		];
	}

	public function validateFromRequest(ServerRequestInterface $request)
	{
		$data = $request->getParsedBody();
		if (empty($data)) {
			return Vagrant::createFromInput([], ['provider' => 'Provider is required']);
		}

		$input =  $this->perform(
			array_merge(
				['provider' => $data['provider']['name'] ?? null],
				$request->getAttribute('route')->getArguments()
			),
			$this->validation
		);

		return $input;
	}
}