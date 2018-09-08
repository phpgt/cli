<?php
namespace Gt\Cli\Argument;

class CommandArgument extends Argument {
	public function __construct(string $commandName) {
		parent::__construct("", $commandName);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}