<?php
namespace Gt\Cli\Test;

use Gt\Cli\Application;
use Gt\Cli\Argument\ArgumentList;
use Gt\Cli\Stream;
use Gt\Cli\Test\Helper\Command\TestCommand;
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

		$this->inPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, Stream::IN]);
		$this->outPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, Stream::OUT]);
		$this->errPath = implode(DIRECTORY_SEPARATOR, [$this->tmp, STREAM::ERROR]);
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

		self::assertStreamContains(
			"Application received no arguments",
			Stream::ERROR
		);
		self::assertStreamEmpty(Stream::OUT);
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

		self::assertStreamContains(
			"Invalid command: \"test-command\"",
			Stream::ERROR
		);
	}

	public function testCommandArgumentsInvalid() {
		/** @var MockObject|ArgumentList $arguments */
		$arguments = self::createMock(ArgumentList::class);
		$arguments->method("getCommandName")
			->willReturn("invalid-test");

		$application = new Application(
			"test-app",
			$arguments,
			new TestCommand("invalid")
		);
		$application->setStream(
			$this->inPath,
			$this->outPath,
			$this->errPath
		);
		$application->run();

		self::assertStreamContains(
			"Usage: invalid-test",
			Stream::ERROR
		);
	}

	protected function assertStreamContains(
		string $message,
		string $streamName
	):void {
		$streamPath = $this->getStreamPathByName($streamName);
		$streamContents = file_get_contents($streamPath);
		self::assertContains(
			$message,
			$streamContents,
			"Stream should contain message."
		);
	}

	protected function assertStreamEmpty(
		string $streamName
	):void {
		$streamPath = $this->getStreamPathByName($streamName);
		$streamContents = trim(file_get_contents($streamPath));
		self::assertEmpty($streamContents);
	}

	protected function getStreamPathByName(string $name):string {
		return implode(DIRECTORY_SEPARATOR, [
			$this->tmp,
			$name,
		]);
	}
}