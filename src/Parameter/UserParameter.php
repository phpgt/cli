<?php
namespace Gt\Cli\Parameter;

class UserParameter extends Parameter {
	public function __construct(
		bool $takesValue,
		string $longOption
	) {
		parent::__construct(
			$takesValue,
			$longOption
		);
	}
}