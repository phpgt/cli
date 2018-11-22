<?php
namespace Gt\Cli\Test;

use Gt\Cli\Argument\Argument;
use Gt\Cli\Argument\ArgumentList;
use PHPUnit\Framework\TestCase;

class ArgumentListTest extends TestCase {
	/** @dataProvider data_randomNamedArgs */
	public function testGetCommandName(string...$args) {
		$argumentList = new ArgumentList(
			array_shift($args),
			...$args
		);
		self::assertEquals(
			$args[0],
			$argumentList->getCommandName()
		);
	}

	/** @dataProvider data_randomNamedArgs */
	public function testIteratorWithNamedArgs(string...$args) {
		$argumentList = new ArgumentList(
			array_shift($args),
			...$args
		);

		foreach($argumentList as $i => $argument) {
			/** @var Argument $argument */
			self::assertInstanceOf(
				Argument::class,
				$argument
			);

			self::assertEquals(
				$args[$i],
				$argument
			);
		}
	}

	/** @dataProvider data_randomLongArgs */
	public function testIteratorWithLongArgs(string...$args) {
		$argumentList = new ArgumentList(
			array_shift($args),
			...$args
		);

		foreach($argumentList as $i => $argument) {
			/** @var Argument $argument */
			self::assertInstanceOf(
				Argument::class,
				$argument
			);

			if($i === 0) {
				self::assertEquals(
					$args[0],
					$argument
				);
				continue;
			}

			$originalKey = $args[($i - 1) * 2 + 1];
			$originalValue = $args[($i - 1) * 2 + 2];

			self::assertEquals(
				substr($originalKey, 2),
				$argument->getKey()
			);
			self::assertEquals(
				$originalValue,
				$argument->getValue()
			);
		}
	}

	public function data_randomNamedArgs():array {
		$dataSet = [];

		for($i = 0; $i < 10; $i++) {
			$params = [];

			$params []= uniqid("script-");
			$params []= uniqid("command-");

			$numParams = rand(1, 10);
			for($j = 0; $j < $numParams; $j++) {
				$params []= uniqid();
			}

			$dataSet []= $params;
		}

		return $dataSet;
	}

	public function data_randomLongArgs():array {
		$dataSet = [];

		for($i = 0; $i < 10; $i++) {
			$params = [];

			$params []= uniqid("script-");
			$params []= uniqid("command-");

			$numParams = rand(1, 10);
			for($j = 0; $j < $numParams; $j++) {
				if($j % 2 === 0) {
					$params []= "--" . uniqid();
				}
				else {
					$params []= uniqid();
				}
			}

			$dataSet []= $params;
		}

		return $dataSet;
	}
}