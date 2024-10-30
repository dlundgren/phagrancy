<?php

/**
 * @file
 * Contains Phagrancy\PackerTest
 */

namespace packer;

use Phagrancy\TestCase\Integration;
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
			self::$process = Process::fromShellCommandline("docker compose up -d");
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

	private function runPacker($file)
	{
		$build = Process::fromShellCommandline("packer build {$file}.json");
		$build->setWorkingDirectory(__DIR__);
		$build->mustRun();

		$curl = Process::fromShellCommandline('curl -s http://127.0.0.1:8080/test/test');
		$curl->mustRun();

		$response = json_decode($curl->getOutput());

		$versions = [];
		foreach ($response->versions as $version) {
			$versions[] = $version->version;
		}

		return $versions;
	}

	public function provideVersions()
	{
		return [
			['v1', '1.2.3-alpha'],
			['v1d', '1.2.3-alpha-direct'],
			['v2', '2.0-beta'],
			['v2d', '2.0-beta-direct'],
		];
	}

	/**
	 * @dataProvider provideVersions
	 */
	public function testPacker($version, $expectedVersion)
	{
		self::assertContains($expectedVersion, $this->runPacker($version));
	}
}
