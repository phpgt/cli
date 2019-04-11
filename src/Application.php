<?php
namespace Gt\Cli;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\Command\Command;
use Gt\Cli\Command\CommandException;
use Gt\Cli\Command\HelpCommand;
use Gt\Cli\Command\InvalidCommandException;
use Gt\Cli\Parameter\MissingRequiredParameterException;
use Gt\Cli\Parameter\MissingRequiredParameterValueException;
use Gt\Cli\Argument\ArgumentList;

class Application {
	protected $description;
	protected $arguments;
	protected $commands;
	protected $stream;
	protected $helpCommand;

	public function __construct(
		string $description,
		ArgumentList $arguments = null,
		Command...$commands
	) {
		$this->description = $description;
		$this->arguments = $arguments;
		$this->commands = $commands;

		$this->helpCommand = new HelpCommand(
			$this->description,
			$arguments->getScript(),
			$this->commands
		);
		$this->commands []= $this->helpCommand;

		$this->stream = new Stream(
			"php://stdin",
			"php://stdout",
			"php://stderr"
		);
		$this->helpCommand->setOutput($this->stream);
	}

	public function setStream($in, $out, $error) {
		$this->stream->setStream($in, $out, $error);
	}

	public function run():void {
		$command = null;
		$exception = null;

		if(is_null($this->arguments)) {
			$this->stream->writeLine(
				"Application has received no commands",
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

			$firstArgument = $argumentValueList->first();
			if($firstArgument) {
				switch($firstArgument->getKey()) {
				case "help":
					$helpArgs = new ArgumentValueList();
					$helpArgs->set("command", $commandName);
					$this->helpCommand->run($helpArgs);
					return;

				case "version":
					$this->stream->writeLine($this->getVersion());
					return;
				}
			}

			$command->checkArguments(
				$this->arguments
			);
			$command->run($argumentValueList);
		}
		catch(MissingRequiredParameterException $exception) {
			$message = "Error - Missing required parameter: "
				. $exception->getMessage();

			$this->stream->writeLine(
				$message,
				Stream::ERROR
			);
		}
		catch(CliException $exception) {
			$this->stream->writeLine(
				$exception->getMessage(),
				Stream::ERROR
			);
		}

		if($exception && $command) {
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

	protected function getVersion():string {
		$version = "Version number not found";

		$dir = __DIR__;
		do {
			$dir = dirname($dir);
			$files = scandir($dir);
		}
		while(!in_array("vendor", $files) || strlen($dir) <= 4);

		$installedJson = implode(DIRECTORY_SEPARATOR, [
			$dir,
			"vendor",
			"composer",
			"installed.json",
		]);
		if(!is_file($installedJson)) {
			return $version;
		}

		$installed = json_decode(file_get_contents($installedJson));

		$scriptName = $this->arguments->getScript();
		$scriptName = pathinfo($scriptName, PATHINFO_FILENAME);

		foreach($installed as $item) {
			$binArray = $item->bin ?? [];
			if(!empty($binArray))
var_dump($binArray);
			foreach($binArray as $bin) {
				$bin = pathinfo($bin, PATHINFO_FILENAME);
				if($bin === $scriptName) {
					$version = $item->version;
					break;
				}
			}
		}

		var_dump($version);die();

		return $version;
	}
}