<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidateTokenOrPasswordTest
 */

namespace Phagrancy\Http\Middleware;

use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotAuthorized;
use Phagrancy\TestCase\Action;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class ValidateTokenOrPasswordTest
	extends TestCase
{
	protected $password = 'test';
	protected $token    = 'coffee';

	public function provideClassSetup()
	{
		return [
			[false, true],
			[true, true],
			[true, false]
		];
	}

	/**
	 * @dataProvider provideClassSetup
	 */
	public function testReturnsNext($useToken, $usePassword)
	{
		$response = $this->runAction(
			$useToken ? $this->token : null,
			$usePassword ? $this->password : null,
			function () {
				return new Json(['hi']);
			});
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());

		Action::assertMessageBodyEqualsJsonArray($response, ["hi"]);
	}

	/**
	 * @dataProvider provideClassSetup
	 */
	public function testReturnsNotAuthorized($useToken, $usePassword)
	{
		$response = $this->runAction(
			$useToken ? 'nope' : null,
			$usePassword ? 'nope' : null,
			function () {
				throw new \RuntimeException("shouldn't reach this");
			});

		self::assertInstanceOf(NotAuthorized::class, $response);
	}

	public function testReturnsNextWithAuthorizationHeader()
	{
		$response = $this->runAction(
			$this->token,
			null,
			function () {
				return new Json(['hi']);
			},
			true);
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());

		Action::assertMessageBodyEqualsJsonArray($response, ["hi"]);
	}

	public function runAction($token, $password, $next, $useHeader = false)
	{
		$e = [
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/',
			'PHP_AUTH_USER'  => 'someone',
			'PHP_AUTH_PW'    => $password
		];

		if ($token !== null) {
			if ($useHeader) {
				$e['HTTP_AUTHORIZATION'] = "Bearer {$token}";
			}
			else {
				$e['REQUEST_URI'] = "/?access_token={$token}";
			}
			unset($e['PHP_AUTH_PW']);
			unset($e['PHP_AUTH_USER']);
		}

		$request = Request::createFromEnvironment(Environment::mock($e));
		$vat     = new ValidateTokenOrPassword($token ? $this->token : null, $password ? $this->password : null);

		return $vat($request, new Response(), $next);
	}
}