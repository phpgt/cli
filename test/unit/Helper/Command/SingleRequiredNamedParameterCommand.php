<?php
namespace Gt\Cli\Test\Helper\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class SingleRequiredNamedParameterCommand extends Command {
	public function __construct() {
		$this->setName("Single Required Name Parameter Command");
		$this->setDescription("A test class for testing purposes.");

		$this->setRequiredNamedParameter("id");
	}

	public function run(ArgumentValueList $arguments):void {
	}
}