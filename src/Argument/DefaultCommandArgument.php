<?php
namespace Gt\Cli\Argument;

class DefaultCommandArgument extends CommandArgument {
	public function __construct(string $defaultCommandName) {
		parent::__construct($defaultCommandName);
	}

	public function set(string $commandName):void {
		$this->value = $commandName;
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}