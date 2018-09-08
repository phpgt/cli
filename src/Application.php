<?php
namespace Gt\Cli;

use Gt\Installer\Argument\ArgumentList;

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

		$this->streams = new Stream(
			"php://stdin",
			"php://stdout",
			"php://stderr"
		);
	}

	public function setStreams($in, $out, $error) {
		$this->streams->setStreams($in, $out, $error);
	}

	public function run():void {
		$commandName = $this->arguments->getCommandName();
		$command = $this->findCommandByName($commandName);
		$command->setStream($this->stream);
		$argumentValueList = $command->getArgumentValueList($this->arguments);

		try {
			$command->checkArguments($this->arguments);
		}
		catch(NotEnoughArgumentsException $exception) {
			$this->streams->writeLine(
				"Not enough arguments passed.",
				Stream::ERROR
			);
			$this->streams->writeLine(
				$command->getUsage(),
				Stream::ERROR
			);
		}
		catch(MissingRequiredParameterException $exception) {

		}
		catch(MissingRequiredParameterValueException $exception) {

		}

		$command->run($argumentValueList);
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