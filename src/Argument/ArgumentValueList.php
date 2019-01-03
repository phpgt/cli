<?php
namespace Gt\Cli\Argument;

class ArgumentValueList {
	protected $valueMap = [];

	public function set(string $key, string $value = null):void {
		$this->valueMap[$key] = $value;
	}

	public function get(string $key):string {
		return $this->valueMap[$key];
	}

	public function contains(string $key):bool {
		return isset($this->valueMap[$key]);
	}
}