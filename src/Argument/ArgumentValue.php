<?php
namespace Gt\Cli\Argument;

class ArgumentValue {
	protected $valueList;
	protected $queueIndex;

	public function __construct(string $value) {
		$this->valueList = [];
		$this->queueIndex = 0;
		$this->push($value);
	}

	public function push(string $value):void {
		$this->valueList []= $value;
	}

	public function get():?string {
		$value = $this->valueList[$this->queueIndex];
		$this->queueIndex++;
		return $value;
	}

	public function getAll():array {
		return $this->valueList;
	}
}