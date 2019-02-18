<?php
namespace Gt\Cli\Argument;

use Iterator;

class ArgumentValueList implements Iterator {
	/** @var ArgumentValue[] */
	protected $valueMap = [];
	protected $iteratorKeys;
	protected $iteratorIndex;

	public function set(string $key, string $value = null):void {
		if(!isset($this->valueMap[$key])) {
			$valueObject = $key === Argument::USER_DATA
				? new NamedArgumentValue($value)
				: new ArgumentValue($value);
			$this->valueMap[$key] = $valueObject;
		}
		else {
			$this->valueMap[$key]->push($value);
		}
	}

	public function get(string $key, string $default = null):string {
		if(!isset($this->valueMap[$key])) {
			if(!is_null($default)) {
				return $default;
			}

			throw new ArgumentValueListNotSetException($key);
		}

		return $this->valueMap[$key];
	}

	public function contains(string $key):bool {
		return isset($this->valueMap[$key]);
	}

	/** @link https://php.net/manual/en/iterator.rewind.php */
	public function rewind() {
		$this->iteratorIndex = 0;
		$this->iteratorKeys = array_keys($this->valueMap);
	}

	/** @link https://php.net/manual/en/iterator.key.php */
	public function key() {
		return $this->iteratorKeys[$this->iteratorIndex];
	}

	/**
	 * @link https://php.net/manual/en/iterator.valid.php
	 */
	public function valid():bool {
		return isset($this->valueMap[
			$this->iteratorKeys[$this->iteratorIndex]
		]);
	}

	/** @link https://php.net/manual/en/iterator.current.php */
	public function current() {
		return $this->valueMap[
			$this->iteratorKeys[$this->iteratorIndex]
		];
	}

	/** @link https://php.net/manual/en/iterator.next.php */
	public function next() {
		$this->iteratorIndex++;
	}
}