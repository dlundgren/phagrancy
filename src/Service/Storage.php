<?php

namespace Phagrancy\Service;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ServerRequestInterface;

class Storage
{
	protected Filesystem $fs;

	public function __construct(Filesystem $fs)
	{
		$this->fs = $fs;
	}

	public function directories(string $path = '.'): array
	{
		$results = [];
		foreach ($this->fs->listContents($path) as $entry) {
			if ($entry instanceof DirectoryAttributes) {
				$results[] = basename($entry->path());
			}
		}
		asort($results);

		return array_values($results);
	}

	public function exists(string $path): bool
	{
		if ($this->fs->fileExists($path)) {
			return true;
		}

		// Older flysystem (PHP < 7.4) doesn't have directoryExists
		try {
			$this->fs->visibility($path);
		}
		catch (FilesystemException $e) {
			return false;
		}

		return true;
	}

	public function files(string $path = '.'): array
	{
		$results = [];
		foreach ($this->fs->listContents($path) as $entry) {
			if ($entry instanceof FileAttributes) {
				$results[] = basename($entry->path());
			}
		}
		asort($results);

		return array_values($results);
	}


	public function saveFromRequest(ServerRequestInterface $request, string $path)
	{
		// get it downloaded
		$tmpFile = $this->createTemporaryFile();
		$request->getBody()->detach();
		$this->fs->writeStream($tmpFile, $stdin = fopen('php://input', 'r'));
		fclose($stdin);

		$this->fs->move($tmpFile, $path);

	}

	public function save($stream, $to) {}

	private function createTemporaryFile()
	{
		return tempnam("/tmp", 'phagrancy');
	}

	public function hasScope(string $scope): bool
	{
		try {
			$this->fs->visibility($scope);
			return true;
		}
		catch (FilesystemException $e) {
			return false;
		}
	}

	public function hasBox(string $scope, string $box): bool
	{
		try {
			$this->fs->visibility("{$scope}/{$box}");
			return true;
		}
		catch (FilesystemException $e) {
			return false;
		}
	}
}