<?php
namespace Gt\Cli\Argument;

abstract class Argument {
	protected $key;
	protected $value;

	public function __construct(string $rawKey, string $value = null) {
		$this->key = $this->processRawKey($rawKey);
		$this->value = $value;
	}

	abstract protected function processRawKey(string $rawKey):string;

	public function getKey():string {
		return $this->key;
	}

	public function getValue():?string {
		return $this->value;
	}
}