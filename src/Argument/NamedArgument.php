<?php
namespace Gt\Cli\Argument;

class NamedArgument extends Argument {
	public function __construct($name) {
		parent::__construct("", $name);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}