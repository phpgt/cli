<?php
namespace Gt\Cli;

use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\Command\Command;
use Gt\Cli\Command\CommandException;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Command\InvalidCommandException;
use Gt\Cli\Parameter\MissingRequiredParameterException;
use Gt\Cli\Parameter\MissingRequiredParameterValueException;
use Gt\Cli\Argument\ArgumentList;

class Application {
	protected $applicationName;
	protected $arguments;
	protected $commands;
	protected $stream;

	public function __construct(
		string $applicationName,
		ArgumentList $arguments,
		Command...$commands
	) {
		$this->applicationName = $applicationName;
		$this->arguments = $arguments;
		$this->commands = $commands;

		$this->commands []= new HelpCommand(
			$this->applicationName,
			$this->commands
		);

		$this->stream = new Stream(
			"php://stdin",
			"php://stdout",
			"php://stderr"
		);
	}

	public function setStream($in, $out, $error) {
		$this->stream->setStream($in, $out, $error);
	}

	public function run():void {
		try {
			$commandName = $this->arguments->getCommandName();
			$command = $this->findCommandByName($commandName);
			$command->setStream($this->stream);
		}
		catch(InvalidCommandException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);

			exit(ErrorCode::get($exception));
		}

		$argumentValueList = $command->getArgumentValueList($this->arguments);

		try {
			$command->checkArguments($this->arguments);
		}
		catch(NotEnoughArgumentsException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
			$this->stream->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);

			exit(ErrorCode::get($exception));
		}
		catch(MissingRequiredParameterException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
			$this->stream->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);

			exit(ErrorCode::get($exception));
		}
		catch(MissingRequiredParameterValueException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
			$this->stream->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);

			exit(ErrorCode::get($exception));
		}

		try {
			$command->run($argumentValueList);
		}
		catch(CommandException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
			$this->stream->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);

			exit(ErrorCode::get($exception));
		}
	}

	protected function findCommandByName(string $name):Command {
		foreach($this->commands as $command) {
			if($command->getName() !== $name) {
				continue;
			}

			return $command;
		}

		throw new InvalidCommandException($name);
	}
}