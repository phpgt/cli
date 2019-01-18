<?php
namespace Gt\Cli\Test;

use Gt\Cli\Application;
use Gt\Cli\Argument\ArgumentList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ApplicationTest extends TestCase {
	protected $tmp;
	protected $inPath;
	protected $outPath;
	protected $errPath;

	public function setUp() {
		$this->tmp = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"cli",
		]);
		if(!is_dir($this->tmp)) {
			mkdir($this->tmp, 0775, true);
		}

		$this->inPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, "in"]);
		$this->outPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, "out"]);
		$this->errPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, "err"]);
		touch($this->inPath);
		touch($this->outPath);
		touch($this->errPath);
	}

	public function tearDown() {
		$fileList = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$this->tmp,
				RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach($fileList as $fileInfo) {
			$function = $fileInfo->isDir()
				? "rmdir"
				: "unlink";

			$function($fileInfo->getRealPath());
		}

		rmdir($this->tmp);
	}

	public function testSetStream() {
		$application = new Application("test-app");
		$application->setStream(
			$this->inPath,
			$this->outPath,
			$this->errPath
		);
		$application->run();

		$out = file_get_contents($this->outPath);
		$err = file_get_contents($this->errPath);
		self::assertContains("Application received no arguments", $err);
		self::assertEmpty($out);
	}

	public function testCommandArgumentInvalid() {
		/** @var ArgumentList|MockObject $arguments */
		$arguments = self::createMock(ArgumentList::class);
		$arguments->method("getCommandName")
			->willReturn("test-command");

		$application = new Application(
			"test-app",
			$arguments
		);
		$application->setStream(
			$this->inPath,
			$this->outPath,
			$this->errPath
		);
		$application->run();

		$err = file_get_contents($this->errPath);
		self::assertContains("Invalid command: \"test-command\"", $err);
	}
}