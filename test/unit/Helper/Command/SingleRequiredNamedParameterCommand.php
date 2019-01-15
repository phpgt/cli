<?php
namespace Gt\Cli\Test\Helper\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class SingleRequiredNamedParameterCommand extends Command {
	public function __construct() {
		$this->setName("single-required-named-parameter-command");
		$this->setDescription("A test class for testing purposes.");

		$this->setRequiredNamedParameter("id");
	}

	public function run(ArgumentValueList $arguments = null):void {
	}
}