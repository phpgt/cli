<?php

namespace Gt\Cli\Test\Argument;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Argument\ArgumentValueListNotSetException;
use PHPUnit\Framework\TestCase;

class ArgumentValueListTest extends TestCase {
	public function testGetSetNotSet() {
		$avl = new ArgumentValueList();
		$this->expectException(ArgumentValueListNotSetException::class);
		$avl->get("test");
	}
}