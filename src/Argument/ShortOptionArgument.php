<?php
namespace Gt\Cli\Argument;

class ShortOptionArgument extends Argument {
	protected function processRawKey(string $rawKey):string {
		return substr($rawKey, 1);
	}
}