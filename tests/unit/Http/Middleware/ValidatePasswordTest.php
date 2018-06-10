<?php

namespace Phagrancy\Http\Middleware;

use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotAuthorized;
use Phagrancy\TestCase\Action;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class ValidatePasswordTest
	extends TestCase
{
	protected $password = 'test';

	public function testRunsNext()
	{
		$response = $this->runAction(
			'test',
			function () {
				return new Json(['hi']);
			});
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());

		Action::assertMessageBodyEqualsJsonArray($response, ["hi"]);
	}

	public function testReturnsNotAuthorized()
	{
		$response = $this->runAction(
			null, function () {
		});
		self::assertInstanceOf(NotAuthorized::class, $response);
	}

	public function testReturnsNextWithoutPassword()
	{
		$e = [
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/',
			'PHP_AUTH_USER'  => 'someone',
			'PHP_AUTH_PW'    => 'test'
		];

		$request = Request::createFromEnvironment(Environment::mock($e));
		$vat     = new ValidatePassword(null, false);

		$response = $vat($request, new Response(), function () { return new Json('skip');});

		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());
		Action::assertMessageBodyEqualsJsonArray($response, 'skip');
	}

	public function runAction($password, $next)
	{
		$e = [
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/',
			'PHP_AUTH_USER'  => 'someone',
			'PHP_AUTH_PW'    => $password
		];

		$request = Request::createFromEnvironment(Environment::mock($e));
		$vat     = new ValidatePassword($this->password, false);

		return $vat($request, new Response(), $next);
	}
}