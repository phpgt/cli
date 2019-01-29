<?php
namespace Gt\Cli\Argument;

class ArgumentValueList {
	/** @var ArgumentValue[] */
	protected $valueMap = [];

	public function set(string $key, string $value = null):void {
		if(!isset($this->valueMap[$key])) {
			$this->valueMap[$key] = new ArgumentValue();
		}

		$this->valueMap[$key]->push($value);
	}

	public function get(string $key):ArgumentValue {
		if(!isset($this->valueMap[$key])) {
			throw new ArgumentValueListNotSetException($key);
		}

		return $this->valueMap[$key];
	}

	public function contains(string $key):bool {
		return isset($this->valueMap[$key]);
	}
}