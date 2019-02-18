<?php
namespace Gt\Cli\Parameter;

class UserParameter extends Parameter {
	public function __construct(
		bool $requireValue,
		string $longOption
	) {
		parent::__construct(
			$requireValue,
			$longOption
		);
	}
}