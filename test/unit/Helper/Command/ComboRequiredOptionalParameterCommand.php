<?php
namespace Gt\Cli\Test\Helper\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class ComboRequiredOptionalParameterCommand extends Command {
	public function __construct() {
		$this->setName("combo-required-optional-parameter-command");
		$this->setDescription("A test class for testing purposes.");

		$this->setRequiredNamedParameter("id");
		$this->setOptionalNamedParameter("name");

		$this->setRequiredParameter(
			true,
			"type",
			"t"
		);

		$this->setOptionalParameter(
			false,
			"verbose",
			"v"
		);
	}

	public function run(ArgumentValueList $arguments):void {
	}
}