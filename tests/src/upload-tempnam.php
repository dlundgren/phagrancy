<?php

/**
 * @file
 * Contains Phagrancy\Action\Api\Scope\Box
 */

namespace Phagrancy\Action\Api\Scope\Box;

// due to an issue with tempnam not working with vfs' we need to make it work
function tempnam($path, $name) {
	return "{$path}/{$name}";
}