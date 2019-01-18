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
		ArgumentList $arguments = null,
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
		$command = null;

		if(is_null($this->arguments)) {
			$this->stream->writeLine(
				"Application received no arguments",
				Stream::ERROR
			);
			return;
		}

		try {
			$commandName = $this->arguments->getCommandName();
			$command = $this->findCommandByName($commandName);
			$command->setOutput($this->stream);

			$argumentValueList = $command->getArgumentValueList(
				$this->arguments
			);
		}
		catch(CliException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
		}

		if(is_null($command)) {
			return;
		}

		try {
			$command->checkArguments(
				$this->arguments
			);
			$command->run($argumentValueList);
		}
		catch(CliException $exception) {
			$this->stream->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);
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