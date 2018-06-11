<?php

/**
 * @file
 * Contains Phagrancy\Http\Middleware\ValidateAccessTokenTest
 */

namespace Phagrancy\Http\Middleware;

use Phagrancy\Http\Response\Json;
use Phagrancy\Http\Response\NotAuthorized;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

class ValidateAccessTokenTest
	extends TestCase
{
	protected $token = 'test';
	public function testRunsNext()
	{
		$response = $this->runAction('test',function() {return new Json(['hi']);});
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());
	}

	public function testReturnsNotAuthorized()
	{
		$response = $this->runAction(null, function() {});
		self::assertInstanceOf(NotAuthorized::class, $response);
	}

	public function runAction($token, $next)
	{
		$e = [
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/'
		];
		if (isset($token)) {
			$e['QUERY_STRING'] = 'access_token=' . $token;
		}

		$request = Request::createFromEnvironment(Environment::mock($e));
		$vat = new ValidateAccessToken($this->token);

		return $vat($request, new Response(), $next);
	}
}