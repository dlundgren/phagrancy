<?php

namespace Phagrancy\Concern;

trait FindsBox
{
	protected function findBox($params, string $storagePath): ?string
	{
		extract($params);

		$box = $this->boxes->ofNameInScope($name, $scope);
		if ($box) {
			$architecture = $architecture ?? 'unknown';
			$path = "{$storagePath}/{$box->path()}/{$version}/{$provider}-{$architecture}.box";
			if (file_exists($path)) {
				return $path;
			}

			if ($architecture === 'unknown') {
				$path = "{$storagePath}/{$box->path()}/{$version}/{$provider}.box";
				if (file_exists($path)) {
					return $path;
				}
			}
		}

		return null;
	}
}