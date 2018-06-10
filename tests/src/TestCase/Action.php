<?php
namespace Phagrancy\TestCase;

use Helmich\Psr7Assert\Psr7Assertions;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Route;

abstract class Action
	extends TestCase
{
	use Psr7Assertions;

	/**
	 * @param string $method
	 * @param string $path
	 * @return ServerRequestInterface
	 */
	protected function buildRequest($method = 'GET', $path = '/')
	{
		$env = Environment::mock([
			'REQUEST_METHOD' => $method,
			'REQUEST_URI' => $path
								 ]);

		return Request::createFromEnvironment($env)
			->withAttribute('route', new Route('GET', '/', function() {}));
	}

	public static function assertMessageBodyEqualsJsonArray(ResponseInterface $response, $json)
	{
		self::hasHeader('content-type', Assert::matchesRegularExpression(',^application/json(;.+)?$,'));

		$b = $response->getBody();
		$b->rewind();
		self::assertEquals(json_decode($b->getContents(), true), $json);
	}
}