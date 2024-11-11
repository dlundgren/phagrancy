<?php

/**
 * @file
 * Contains Phagrancy\Concern\FindsBox
 */

namespace Phagrancy\Concern;

use Phagrancy\Http\Context\Vagrant;
use Phagrancy\Service\Storage;

trait FindsBox
{
	protected function findBox(Vagrant $params, Storage $storage): ?string
	{
		if ($box = $this->boxes->ofNameInScope($params->name, $params->scope)) {
			$architecture = $params->architecture ?? 'unknown';
			if ($storage->exists($path = "{$box->path()}/{$params->version}/{$params->provider}-{$architecture}.box")) {
				$file = $path;
			}
			elseif ($architecture === 'unknown') {
				if ($storage->exists($path = "{$box->path()}/{$params->version}/{$params->provider}.box")) {
					$file = $path;
				}
			}
		}

		return empty($file)
			? null
			: $storage->filePath($file);
	}
}