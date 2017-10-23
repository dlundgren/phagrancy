<?php

use Phagrancy\Action;

$rootPath = dirname(__DIR__);

require "$rootPath/vendor/autoload.php";

$container = new Slim\Container();
(new \Phagrancy\ServiceProvider\Pimple($rootPath))->register($container);

$app = new \Slim\App($container);

// vagrant-cloud/atlas api for uploading new boxes
$app->group('/api/v1/box/{scope}', function () {
	$this->get('', Action\Api\Scope\Index::class);
	$this->group('/{name}', function () {
		$this->get('', Action\Api\Scope\Box\Definition::class);
		$this->post('/versions', Action\Api\Scope\Box\CreateVersion::class);
		$this->group(
			'/version/{version}', function () {
			$this->post('', Action\AllClear::class);
			$this->post('/providers', Action\Api\Scope\Box\CreateProvider::class);
			$this->put('/release', Action\AllClear::class);
			$this->group(
				'/provider/{provider}', function () {
				$this->get('', Action\Api\Scope\Box\SendFile::class);
				$this->delete('', Action\AllClear::class);
				$this->get('/upload', Action\Api\Scope\Box\UploadPreFlight::class);
				$this->put('/upload', Action\Api\Scope\Box\Upload::class);
			});
		});
	});
})->add($container[\Phagrancy\Http\Middleware\ValidateAccessToken::class]);

// regular usage
$app->group('/{scope}', function () {
	$this->get('', Action\Scope\Index::class);
	$this->group('/{name}', function () {
		$this->get('', Action\Scope\Box\Definition::class);
		$this->get('/{version}/{provider}', Action\Scope\Box\SendFile::class);
	});
})->add($container[\Phagrancy\Http\Middleware\ValidatePassword::class]);

$app->run();