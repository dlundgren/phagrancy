<?php

/**
 * @file
 * Contains Phagrancy\TestCase\Action
 */

namespace Phagrancy\TestCase;

use Helmich\Psr7Assert\Psr7Assertions;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Route;

/**
 * Abstract TestCase
 *
 * @package Phagrancy\TestCase
 */
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

	public static function assertResponseJsonEqualsString($response, $path, $value)
	{
		$b = $response->getBody();
		$b->rewind();
		$json = json_decode($b->getContents(), true);

		$keys = explode('.', $path);
		foreach ($keys as $key) {
			if (array_key_exists($key, $json)) {
				$json =& $json[$key];
			}
		}

		self::assertSame((string)$value, $json, "JSON at $path does not match string `$value`");
	}

	public static function assertMessageBodyEqualsJsonArray(ResponseInterface $response, $json)
	{
		self::hasHeader('content-type', Assert::matchesRegularExpression(',^application/json(;.+)?$,'));

		$b = $response->getBody();
		$b->rewind();
		self::assertEquals(json_decode($b->getContents(), true), $json);
	}
}