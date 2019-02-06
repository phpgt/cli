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
		$this->writeLine("Command running successfully");

		$this->writeLine(
			"Command ID: "
			. $arguments->get("id")
		);

		if($arguments->contains("option")) {
			$this->writeLine(
				"Option: " . $arguments->get("option")
			);
		}
		else {
			$this->writeLine("No Option set");
		}

		$this->writeLine(
			"Must-have-value: "
			. $arguments->get("must-have-value")
		);

		if($arguments->contains("no-value")) {
			$this->writeLine("No-value argument set");
		}
		else {
			$this->writeLine("No-value argument not set");
		}
	}
}