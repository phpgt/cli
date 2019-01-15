<?php
namespace Gt\Cli\Test\Command;

use Gt\Cli\Command\Command;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HelpCommandTest extends TestCase {
	public function testRun() {
		/** @var Command[]|MockObject[] $applicationCommandList */
		$applicationCommandList = [];
		$applicationCommandList []= self::createMock(Command::class);
		$applicationCommandList []= self::createMock(Command::class);
		$applicationCommandList []= self::createMock(Command::class);

		$applicationCommandList[0]->method("getName")
			->willReturn("first");
		$applicationCommandList[0]->method("getDescription")
			->willReturn("The first command");
		$applicationCommandList[1]->method("getName")
			->willReturn("second");
		$applicationCommandList[1]->method("getDescription")
			->willReturn("The second command");
		$applicationCommandList[2]->method("getName")
			->willReturn("third");
		$applicationCommandList[2]->method("getDescription")
			->willReturn("The third command");

		/** @var Stream|MockObject $stream */
		$stream = $this->createMock(Stream::class);
		$buffer = [
			Stream::OUT => [],
			Stream::ERROR => [],
		];
		$stream->method("write")
			->willReturnCallback(function($message, $streamName)use(&$buffer) {
				$buffer[$streamName] []= $message;
			});

		$command = new HelpCommand(
			"Test application",
			$applicationCommandList
		);

		$command->setOutput($stream);
		$command->run();

		self::assertEmpty($buffer[Stream::ERROR]);
		self::assertNotEmpty($buffer[Stream::OUT]);
		$out = implode("", $buffer[Stream::OUT]);

		self::assertRegExp(
			"/•\s+first\s+The first command/",
			$out
		);
		self::assertRegExp(
			"/•\s+second\s+The second command/",
			$out
		);
		self::assertRegExp(
			"/•\s+third\s+The third command/",
			$out
		);
	}
}