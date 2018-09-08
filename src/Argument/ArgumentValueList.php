<?php
namespace Gt\Cli\Argument;

class ArgumentValueList {
	protected $valueMap = [];

	public function set(string $key, string $value):void {
		$this->valueMap[$key] = $value;
	}

	public function get(string $key):string {
		return $this->valueMap[$key];
	}
}