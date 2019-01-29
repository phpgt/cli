<?php
namespace Gt\Cli\Argument;

class ArgumentValueList {
	protected $valueMap = [];

	public function set(string $key, string $value = null):void {
// TODO: Issue #17 can convert existing values to arrays here.
		$this->valueMap[$key] = $value;
	}

	public function get(string $key):string {
		if(!isset($this->valueMap[$key])) {
			throw new ArgumentValueListNotSetException($key);
		}
		return $this->valueMap[$key];
	}

	public function contains(string $key):bool {
		return isset($this->valueMap[$key]);
	}
}