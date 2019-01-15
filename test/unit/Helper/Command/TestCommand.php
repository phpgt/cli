<?php
namespace Gt\Cli\Test\Helper\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class TestCommand extends Command {
	public function __construct(string $prefix = null) {
		if(!is_null($prefix)) {
			$prefix .= "-";
		}

		$this->setName("{$prefix}test");
		$this->setDescription("A test command for unit testing");

		$this->setRequiredNamedParameter("id");
		$this->setOptionalNamedParameter("option");
		$this->setRequiredParameter(
			true,
			"must-have-value",
			"m"
		);
		$this->setOptionalParameter(
			false,
			"no-value",
			"n"
		);
	}

	public function run(ArgumentValueList $arguments = null):void {
	}
}