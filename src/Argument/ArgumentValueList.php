<?php
namespace Gt\Cli\Argument;

use Iterator;

class ArgumentValueList implements Iterator {
	const DEFAULT_ARGUMENT_VALUE = "//\\DEFAULT ARGUMENT VALUE\\//";

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

	public function get(
		string $key,
		$default = self::DEFAULT_ARGUMENT_VALUE
	):ArgumentValue {
// $default is handled in this manner because we want to allow null to be a
// valid value for the default.
		if(!$this->contains($key)) {
			if($default !== self::DEFAULT_ARGUMENT_VALUE) {
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
		if(!$this->iteratorIndex) {
			$this->rewind();
		}
		return $this->valueList[$this->iteratorIndex] ?? null;
	}

	/** @link https://php.net/manual/en/iterator.next.php */
	public function next() {
		$this->iteratorIndex++;
	}

	public function first() {
		return $this->valueList[0] ?? null;
	}
}