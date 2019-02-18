<?php
namespace Gt\Cli\Argument;

use Iterator;

class ArgumentValueList implements Iterator {
	/** @var ArgumentValue[] */
	protected $valueList = [];
	protected $argumentValueMap = [];
	protected $iteratorIndex;

	public function set(string $key, string $value = null):void {
		if($this->contains($key)) {
			$valueObject = $this->get($key);
		}
		else {
			$valueObject = $key === Argument::USER_DATA
				? new NamedArgumentValue($key)
				: new ArgumentValue($key);
			$this->valueList []= $valueObject;
		}

		$valueObject->push($value);
		$this->argumentValueMap[$key] = $valueObject;
	}

	public function get(string $key, string $default = null):ArgumentValue {
		if(!$this->contains($key)) {
			if(!is_null($default)) {
				return new DefaultArgumentValue($default);
			}

			throw new ArgumentValueListNotSetException($key);
		}

		return $this->argumentValueMap[$key];
	}

	public function contains(string $key):bool {
		return isset($this->argumentValueMap[$key]);
	}

	/** @link https://php.net/manual/en/iterator.rewind.php */
	public function rewind() {
		$this->iteratorIndex = 0;
	}

	/** @link https://php.net/manual/en/iterator.key.php */
	public function key() {
		return $this->iteratorIndex;
	}

	/**
	 * @link https://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		return isset($this->valueList[$this->iteratorIndex]);
	}

	/** @link https://php.net/manual/en/iterator.current.php */
	public function current() {
		return $this->valueList[$this->iteratorIndex];
	}

	/** @link https://php.net/manual/en/iterator.next.php */
	public function next() {
		$this->iteratorIndex++;
	}
}