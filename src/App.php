<?php

/**
 * @file
 * Contains Phagrancy\App
 */

namespace Phagrancy;

use Phagrancy\Action\AllClear;
use Phagrancy\Http\Middleware;

/**
 * Phagrancy core application
 *
 * This extends Slim\App to make testing easier
 *
 * @package Phagrancy
 */
class App
	extends \Slim\App
{
	public function __construct($container = [])
	{
		parent::__construct($container);

		// @formatter:off

		// vagrant-cloud/atlas api for uploading new boxes
		$this->group('/api/v1/box/{scope}', function () {
			$this->get('', Action\Api\Scope\Index::class);
			$this->group('/{name}', function () {
				$this->get('', Action\Api\Scope\Box\Definition::class);
				$this->post('/versions', Action\Api\Scope\Box\CreateVersion::class);
				$this->group('/version/{version}', function () {
					$this->post('/providers', Action\Api\Scope\Box\CreateProvider::class);
					$this->put('/release', Action\AllClear::class);
					$this->group('/provider/{provider}', function () {
						$this->get('', Action\Api\Scope\Box\SendFile::class);
						$this->delete('', Action\AllClear::class);
						$this->get('/upload', Action\Api\Scope\Box\UploadPreFlight::class);
						$this->put('/upload', Action\Api\Scope\Box\Upload::class);
					});
				});
			});
		})->add($container[Middleware\ValidateAccessToken::class]);

		// frontend
		$this->get('/scopes', Action\Scopes::class);
		$this->group('/{scope}', function () {
			$this->get('', Action\Scope\Index::class);
			$this->group('/{name}', function () {
				$this->get('', Action\Scope\Box\Definition::class);
				$this->get('/{version}/{provider}', Action\Scope\Box\SendFile::class);
			});
		})->add($container[Middleware\ValidateTokenOrPassword::class]);

		$this->get('/', AllClear::class);

		// @formatter:on
	}
}