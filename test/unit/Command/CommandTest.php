<?php
namespace Gt\Cli\Test\Command;

use ArrayIterator;
use Gt\Cli\Argument\Argument;
use Gt\Cli\Argument\ArgumentList;
use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Argument\CommandArgument;
use Gt\Cli\Argument\NamedArgument;
use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\CliException;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Stream;
use Gt\Cli\Test\Helper\Command\SingleRequiredNamedParameterCommand;
use Gt\Cli\Test\Helper\Command\TestCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase {
	public function testSetOutput() {
		/** @var Stream|MockObject $stream */
		$stream = $this->createMock(Stream::class);
		/** @var ArgumentValueList|MockObject $args */
		$args = $this->createMock(ArgumentValueList::class);

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

	public function testGetName() {
		$command = new TestCommand();
		self::assertEquals("test", $command->getName());

		foreach(["first", "second", "third"] as $prefix) {
			$command = new TestCommand($prefix);

			self::assertEquals(
				"{$prefix}-test",
				$command->getName()
			);
		}
	}

	public function testGetDescription() {
		$command = new TestCommand();
		self::assertEquals(
			"A test command for unit testing",
			$command->getDescription()
		);
	}

	public function testCheckArgumentsNamedArgumentGood() {
		$args = [
			self::createMock(NamedArgument::class),
		];

		/** @var ArgumentList|MockObject $argList */
		$argList = $this->createIteratorMock(
			ArgumentList::class,
			$args
		);

		$command = new SingleRequiredNamedParameterCommand();
		$exception = null;

		try {
			$command->checkArguments($argList);
		}
		catch(CliException $exception) {}
		self::assertNull($exception, "No exception should be thrown");
	}

	public function testCheckArgumentsNamedArgumentBad() {
// The first and only argument is now an incorrect "CommandArgument".
		$args = [
			self::createMock(CommandArgument::class),
		];

		/** @var ArgumentList|MockObject $argList */
		$argList = $this->createIteratorMock(
			ArgumentList::class,
			$args
		);

		$command = new SingleRequiredNamedParameterCommand();
		$this->expectException(NotEnoughArgumentsException::class);
		$command->checkArguments($argList);
	}

	protected function createIteratorMock(
		string $className,
		array $items = []
	):MockObject {
		$mock = $this->createMock($className);
		$iterator = new ArrayIterator($items);

		$mock->method("rewind")
			->willReturnCallback(function()use($iterator) {
				$iterator->rewind();
			});
		$mock->method("current")
			->willReturnCallback(function()use($iterator) {
				return $iterator->current();
			});
		$mock->method("key")
			->willReturnCallback(function()use($iterator) {
				return $iterator->key();
			});
		$mock->method("next")
			->willReturnCallback(function()use($iterator) {
				$iterator->next();
			});
		$mock->method("valid")
			->willReturnCallback(function()use($iterator) {
				return $iterator->valid();
			});

		return $mock;
	}
}