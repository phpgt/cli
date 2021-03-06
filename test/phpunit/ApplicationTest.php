<?php
namespace Gt\Cli\Test;

use Gt\Cli\Application;
use Gt\Cli\Argument\ArgumentList;
use Gt\Cli\Argument\LongOptionArgument;
use Gt\Cli\Argument\NamedArgument;
use Gt\Cli\Stream;
use Gt\Cli\Test\Helper\ArgumentMockTestCase;
use Gt\Cli\Test\Helper\Command\TestCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ApplicationTest extends ArgumentMockTestCase {
	protected $tmp;
	protected $inPath;
	protected $outPath;
	protected $errPath;

	public function setUp():void {
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

	public function tearDown():void {
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
			"Application has received no commands",
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

	public function testCommandRun() {
		$idArgument = self::createMock(NamedArgument::class);
		$idArgument->method("getValue")
			->willReturn("abcde");
		$mustHaveValueArgument = self::createMock(LongOptionArgument::class);
		$mustHaveValueArgument->method("getKey")
			->willReturn("must-have-value");
		$mustHaveValueArgument->method("getValue")
			->willReturn("1234");

		$args = [
			$idArgument,
			$mustHaveValueArgument,
		];
		$longArgs = [
			"abcde",
			["must-have-value" => "1234"],
		];

		/** @var MockObject|ArgumentList $arguments */
		$arguments = self::createArgumentListMock(
			$args,
			$longArgs
		);

		$arguments->method("getCommandName")
			->willReturn("valid-test");

		$application = new Application(
			"test-app",
			$arguments,
			new TestCommand("valid")
		);
		$application->setStream(
			$this->inPath,
			$this->outPath,
			$this->errPath
		);
		$application->run();

		self::assertStreamEmpty(Stream::ERROR);

		self::assertStreamContains(
			"Command ID: abcde",
			Stream::OUT
		);
		self::assertStreamContains(
			"Command running successfully",
			Stream::OUT
		);
		self::assertStreamContains(
			"No Option set",
			Stream::OUT
		);
		self::assertStreamContains(
			"Must-have-value: 1234",
			Stream::OUT
		);
		self::assertStreamContains(
			"No-value argument not set",
			Stream::OUT
		);
	}

	protected function assertStreamContains(
		string $message,
		string $streamName
	):void {
		$streamPath = $this->getStreamPathByName($streamName);
		$streamContents = file_get_contents($streamPath);
		self::assertStringContainsString(
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
		self::assertEmpty($streamContents, "Contents: \"$streamContents\"");
	}

	protected function getStreamPathByName(string $name):string {
		return implode(DIRECTORY_SEPARATOR, [
			$this->tmp,
			$name,
		]);
	}
}