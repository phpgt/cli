<?php

namespace Gt\Cli\Test;

use Gt\Cli\Argument\ShortOptionArgument;
use PHPUnit\Framework\TestCase;

class ArgumentTest extends TestCase {
	public function testShortOption() {
		$value = uniqid();
		$arg = new ShortOptionArgument("-d", $value);

		self::assertEquals("d", $arg->getKey());
		self::assertEquals($value, $arg->getValue());
	}
}