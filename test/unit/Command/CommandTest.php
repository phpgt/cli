<?php
namespace Gt\Cli\Test\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase {
	public function testSetOutput() {
		/** @var Stream|MockObject $stream */
		$stream = self::createMock(Stream::class);
		/** @var ArgumentValueList|MockObject $args */
		$args = self::createMock(ArgumentValueList::class);

		$buffer = [
			Stream::OUT => [],
			Stream::ERROR => [],
		];
		$stream->method("write")
			->willReturnCallback(function($message, $streamName)use(&$buffer) {
				$buffer[$streamName] []= $message;
			});

		$command = new HelpCommand("UnitTest");
		$command->run($args);
		self::assertEmpty( $buffer[Stream::OUT]);

		$command->setOutput($stream);
		$command->run($args);
		self::assertNotEmpty($buffer[Stream::OUT]);
		self::assertEmpty($buffer[Stream::ERROR]);

	}
}