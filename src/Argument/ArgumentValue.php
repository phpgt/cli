<?php
namespace Gt\Cli\Argument;

class ArgumentValue {
	protected $valueArray = [];

	public function __toString():string {
		return implode(PHP_EOL, $this->valueArray);
	}

	public function isSingleValue():bool {
		return count($this->valueArray) === 1;
	}

	public function isMultipleValue():bool {
		return !$this->isSingleValue();
	}

	public function push(string $value):void {
		$this->valueArray []= $value;
	}

	public function shift():?string {
		return array_shift($this->valueArray);
	}

	public function getAll():array {
		return $this->valueArray;
	}
}