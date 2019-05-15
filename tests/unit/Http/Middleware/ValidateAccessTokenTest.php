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

	public function testRunsNextWithQueryParam()
	{
		$response = $this->runAction('test',function() {return new Json(['hi']);});
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());
	}

	public function testReturnsNotAuthorizedWithQueryParam()
	{
		$response = $this->runAction(null, function() {});
		self::assertInstanceOf(NotAuthorized::class, $response);
	}

	public function testRunsNextWithHeader()
	{
		$response = $this->runAction('test',function() {return new Json(['hi']);}, true);
		self::assertInstanceOf(Json::class, $response);
		self::assertEquals(200, $response->getStatusCode());
	}

	public function runAction($token, $next, $useHeader = null)
	{
		$e = [
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI'    => '/'
		];

		if (isset($token)) {
			if ($useHeader) {
				$e['HTTP_AUTHORIZATION'] = "Bearer {$token}";
			}
			else {
				$e['QUERY_STRING'] = 'access_token=' . $token;
			}
		}

		$request = Request::createFromEnvironment(Environment::mock($e));
		$vat = new ValidateAccessToken($this->token);

		return $vat($request, new Response(), $next);
	}
}