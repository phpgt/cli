<?php
namespace Gt\Cli\Test;

use Gt\Cli\Argument\ArgumentList;
use PHPUnit\Framework\TestCase;

class ArgumentListTest extends TestCase {
	/** @dataProvider data_randomArgs */
	public function testGetCommandName(string...$args) {
		$argumentList = new ArgumentList(
			array_shift($args),
			...$args
		);

	}

	public function data_randomArgs():array {
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
}