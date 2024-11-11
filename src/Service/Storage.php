<?php

/**
 * @file
 * Contains Phagrancy\Service\Storage
 */

namespace Phagrancy\Service;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ServerRequestInterface;

class Storage
{
	protected Filesystem $fs;

	protected string $path;

	public function __construct(Filesystem $fs, string $path)
	{
		$this->fs = $fs;
		$this->path = $path;
	}

	public function all(string $path = '.'): array
	{
		$results = [];
		foreach ($this->fs->listContents($path)->sortByPath() as $entry) {
			$results[] = basename($entry->path());
		}

		return $results;
	}

	public function delete(string $path): bool
	{
		$working = str_starts_with($path, $this->path)
			? str_replace($this->path, '', $path)
			: $path;

		if ($this->exists($working) && is_writable($path)) {
			$this->fs->delete($working);

			return true;
		}

		return false;
	}

	public function directories(string $path = '.'): array
	{
		$results = [];
		foreach ($this->fs->listContents($path)->sortByPath() as $entry) {
			if ($entry instanceof DirectoryAttributes) {
				$results[] = basename($entry->path());
			}
		}

		return $results;
	}

	public function exists(string $path): bool
	{
		if ($this->fs->fileExists($path)) {
			return true;
		}

		// flysystem < 3.x doesn't have directoryExists
		try {
			$this->fs->visibility($path);
		}
		catch (FilesystemException $e) {
			return false;
		}

		return true;
	}

	public function filePath(string $file): ?string
	{
		return $this->exists($file)
			? "{$this->path}/{$file}"
			: null;
	}

	public function files(string $path = '.'): array
	{
		$results = [];
		foreach ($this->fs->listContents($path)->sortByPath() as $entry) {
			if ($entry instanceof FileAttributes) {
				$results[] = basename($entry->path());
			}
		}

		return $results;
	}

	public function isAvailable(): bool
	{
		return is_writable($this->path) || is_writable(dirname($this->path));
	}

	public function write(string $path, $content): bool
	{
		$this->fs->write($path, $content);

		return true;
	}

	public function saveFromRequest(ServerRequestInterface $request, string $path): bool
	{
		$request->getBody()->detach();
		$stdin = fopen('php://input', 'r');

		try {
			$this->fs->writeStream($path, $stdin);
		}
		finally {
			fclose($stdin);
		}

		return true;
	}
}