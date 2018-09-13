<?php
namespace Gt\Cli;

use RuntimeException;

class CliException extends RuntimeException {
	public function __construct(string $message) {
		$code = ErrorCode::get(get_class($this));

		parent::__construct(
			$message,
			$code
		);
	}
}