<?php
namespace Gt\Cli;

use Exception;
use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\Command\CommandException;
use Gt\Cli\Command\InvalidCommandException;
use Gt\Cli\Parameter\MissingRequiredParameterException;
use Gt\Cli\Parameter\MissingRequiredParameterValueException;

class ErrorCode {
	const DEFAULT_CODE = 1000;

	protected static $classList = [
		"NO_ERROR",
		NotEnoughArgumentsException::class,
		CommandException::class,
		InvalidCommandException::class,
		MissingRequiredParameterValueException::class,
		MissingRequiredParameterException::class,
	];

	public static function get(string $exceptionClassName):int {
		return array_search(
			$exceptionClassName,
			self::$classList
		) ?: self::DEFAULT_CODE;
	}
}