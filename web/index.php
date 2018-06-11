<?php

/**
 * Serving static files when the server will run via PHP CLI server
 */
if (PHP_SAPI == 'cli-server') {
	$url  = parse_url($_SERVER['REQUEST_URI']);
	$file = __DIR__ . $url['path'];
	if (is_file($file)) {
		return false;
	}
}

/** @var string $rootPath */
$rootPath = dirname(__DIR__);

require "$rootPath/vendor/autoload.php";

$container = new Slim\Container();
(new \Phagrancy\ServiceProvider\Pimple($rootPath))->register($container);

$app = new \Phagrancy\App($container);

$app->run();