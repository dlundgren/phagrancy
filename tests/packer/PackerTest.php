<?php

/**
 * @file
 * Contains Phagrancy\PackerTest
 */

namespace Phagrancy;

use Phagrancy\TestCase\Integration;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Tests against packer itself
 */
class PackerTest
	extends Integration
{
	protected static $bin;
	protected static $path;
	protected static $port = 8080;
	protected static $process;
	protected static $useDockerCompose;

	public static function setUpBeforeClass(): void
	{
		self::$bin = (new ExecutableFinder())->find('packer');
		if (empty(self::$bin)) {
			self::fail("Missing packer binary");
		}

		self::$useDockerCompose = getenv('PHAGRANCY_TEST_USE_DOCKER_COMPOSE');
		if (self::$useDockerCompose) {
			self::$port = self::findAvailablePort(8080);
			self::$process = Process::fromShellCommandline(
				     "docker compose up -d",
				env: [
					     'PHP_VERSION' => sprintf("%s.%s", PHP_MAJOR_VERSION, PHP_MINOR_VERSION),
					     'TEST_PORT'   => self::$port
				     ]
			);
			self::$process->setWorkingDirectory(__DIR__);
			self::$process->mustRun();
			sleep(5);
		}
	}

	public static function tearDownAfterClass(): void
	{
		if (self::$useDockerCompose) {
			$down = Process::fromShellCommandline('docker compose down');
			$down->setWorkingDirectory(__DIR__);
			$down->mustRun();
		}
	}

	private static function findAvailablePort($startAt)
	{
		$port = $startAt;
		for ($max = 0; $max < 10; $max++) {
			$connection = @fsockopen('localhost', $port, $errno, $errstr, 1);

			if (is_resource($connection)) {
				fclose($connection);
				$port++;
			}
			else {
				return $port;
			}
		}
		throw new \RuntimeException("Unable to find a useable port starting at {$startAt}");
	}

	public static function provideVersions()
	{
		return [
			['v1', false, '1.2.3-alpha'],
			['v1', true, '1.2.3-direct'],
			['v2', false, '2.0-beta'],
			['v2', true, '2.0-direct']
		];
	}

	/**
	 * @dataProvider provideVersions
	 */
	#[DataProvider('provideVersions')]
	public function testPacker($apiVersion, $directUpload, $expectedVersion)
	{
		$port = self::$port;
		$build  = new Process(
			[
				// @formatter:off
				"packer", "build",
				"-var", "api_version={$apiVersion}",
				"-var", "port={$port}",
				"-var", "direct_upload=" . ($directUpload ? 'true' : 'false'),
				"-var", "version={$expectedVersion}",
				"test.pkr.hcl"
				// @formatter:on
			]);

		$build->setWorkingDirectory(__DIR__);
		$build->mustRun();

		$curl = Process::fromShellCommandline("curl -s http://127.0.0.1:{$port}/test/test");
		$curl->mustRun();

		$response = json_decode($curl->getOutput());

		$versions = [];
		foreach ($response->versions as $version) {
			$versions[] = $version->version;
		}

		self::assertContains($expectedVersion, $versions);
	}
}
