<?php
namespace Gt\Cli\Test\Command;

use ArrayIterator;
use Gt\Cli\Argument\Argument;
use Gt\Cli\Argument\ArgumentList;
use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Argument\CommandArgument;
use Gt\Cli\Argument\LongOptionArgument;
use Gt\Cli\Argument\NamedArgument;
use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\CliException;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Parameter\MissingRequiredParameterException;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;
use Gt\Cli\Test\Helper\Command\MultipleRequiredParameterCommand;
use Gt\Cli\Test\Helper\Command\SingleRequiredNamedParameterCommand;
use Gt\Cli\Test\Helper\Command\TestCommand;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
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

	public function testCheckArgumentsSingleGood() {
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

	public function testCheckArgumentsSingleBad() {
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

	public function testCheckArgumentsMultipleGood() {
		$args = [
			self::createMock(NamedArgument::class),
			self::createMock(NamedArgument::class),
			self::createMock(LongOptionArgument::class),
			self::createMock(LongOptionArgument::class),
		];
		$longArgs = [
			null,
			null,
			["framework" => "php.gt"],
			"example"
		];

		/** @var ArgumentList|MockObject $argList */
		$argList = $this->createArgumentListMock(
			$args,
			$longArgs
		);

		$command = new MultipleRequiredParameterCommand();

		$exception = null;
		try {
			$command->checkArguments($argList);
		}
		catch(CliException $exception) {}
		self::assertNull($exception, "No exception should be thrown");
	}

	public function testCheckArgumentsMultipleBad() {
// Almost identical test to above, but the "framework" arg will not be provided.
		$args = [
			self::createMock(NamedArgument::class),
			self::createMock(NamedArgument::class),
			self::createMock(LongOptionArgument::class),
			self::createMock(LongOptionArgument::class),
		];
		$longArgs = [
			null,
			null,
			["age" => "123"],
			"example"
		];

		/** @var ArgumentList|MockObject $argList */
		$argList = $this->createArgumentListMock(
			$args,
			$longArgs
		);

		$command = new MultipleRequiredParameterCommand();
		$this->expectException(MissingRequiredParameterException::class);
		$command->checkArguments($argList);
	}

	public function testGetRequiredNamedParameterList() {
		$command = new MultipleRequiredParameterCommand();
		$list = $command->getRequiredNamedParameterList();
		$requiredNames = [];

		foreach($list as $item) {
			$requiredNames []= $item->getOptionName();
		}

		self::assertContains("id", $requiredNames);
		self::assertContains("name", $requiredNames);
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

	protected function createArgumentListMock(
		array $items = [],
		array $longArgs = []
	):MockObject {
		$argList = $this->createIteratorMock(
			ArgumentList::class,
			$items
		);

		$argList->method("contains")
			->willReturnCallback(function(Parameter $param)use($longArgs) {
				$longOption = $param->getLongOption();
				foreach($longArgs as $a) {
					if(is_array($a)) {
						if(key($a) === $longOption) {
							return true;
						}
					}
					else {
						if($a === $longOption) {
							return true;
						}
					}
				}
				return false;
			});

		$argList->method("getValueForParameter")
			->willReturnCallback(function(Parameter $param)use($longArgs) {
				$longOption = $param->getLongOption();
				foreach($longArgs as $a) {
					if(!is_array($a)) {
						continue;
					}

					$key = key($a);
					if($key !== $longOption) {
						continue;
					}

					return $a[$key];
				}
				return null;
			});

		return $argList;
	}
}