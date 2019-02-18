<?php
namespace Gt\Cli\Argument;

class ArgumentValue {
	protected $key;
	protected $valueList;
	protected $queueIndex;

	public function __construct(string $key) {
		$this->key = $key;
		$this->valueList = [];
		$this->queueIndex = 0;
	}

	public function push(string $value = null):void {
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