<?php
namespace Gt\Cli\Parameter;

class Parameter {
	protected $takesValue;
	protected $longOption;
	protected $shortOption;
	protected $example;

	public function __construct(
		bool $takesValue,
		string $longOption,
		string $shortOption = null,
		string $example = null
	) {
		$this->takesValue = $takesValue;
		$this->longOption = $longOption;
		$this->shortOption = $shortOption;
		$this->example = $example;
	}

	public function __toString():string {
		$message = $this->longOption;

		if(!is_null($this->shortOption)) {
			$message .= " (";
			$message .= $this->shortOption;
			$message .= ")";
		}

		return $message;
	}

	public function getLongOption():string {
		return $this->longOption;
	}

	public function getShortOption():?string {
		return $this->shortOption;
	}

	public function takesValue():bool {
		return $this->takesValue;
	}

	public function getExample():string {
		return $this->example
			?? strtoupper($this->longOption) . "_VALUE";
	}
}