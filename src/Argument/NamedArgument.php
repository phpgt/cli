<?php
namespace Gt\Cli\Argument;

class NamedArgument extends Argument {
	public function __construct(string $value) {
		parent::__construct("", $value);
	}

	protected function processRawKey(string $rawKey): string {
		return "";
	}
}
