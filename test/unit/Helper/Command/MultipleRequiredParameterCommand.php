<?php
namespace Gt\Cli\Test\Helper\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class MultipleRequiredParameterCommand extends Command {
	public function __construct() {
		$this->setName("Multiple Required Parameter Command");
		$this->setDescription("A test class for testing purposes.");

		$this->setRequiredNamedParameter("id");
		$this->setRequiredNamedParameter("name");
		$this->setRequiredParameter(
			true,
			"framework",
			"f"
		);
		$this->setRequiredParameter(
			false,
			"example"
		);
	}

	public function run(ArgumentValueList $arguments):void {
	}
}