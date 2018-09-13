<?php
namespace Gt\Cli\Argument;

use Gt\Cli\CliException;

class UnexpectedArgumentException extends CliException {
	public function __construct($argumentName) {
		parent::__construct("Unexpected argument: $argumentName.");
	}
}