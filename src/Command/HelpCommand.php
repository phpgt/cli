<?php
namespace Gt\Cli\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class HelpCommand extends Command {
	protected $applicationName;
	/** @var Command[] */
	protected $applicationCommandList;

	/**
	 * @param Command[] $applicationCommandList
	 */
	public function __construct(
		string $applicationName,
		array $applicationCommandList = []
	) {
		$this->applicationName = $applicationName;
		$this->applicationCommandList = $applicationCommandList;
		$this->applicationCommandList []= $this;
	}

	public function getName():string {
		return "help";
	}

	public function getDescription():string {
		return "Display information about available commands";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [
			new NamedParameter("command"),
		];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [];
	}

	public function run(ArgumentValueList $arguments = null): void {
		$command = (string)$arguments->get("command", "*");
		var_dump($command);die();

		$this->writeLine($this->applicationName);
		$this->writeLine();

		if(empty($this->applicationCommandList)) {
			$this->writeLine(
				"There are no commands available"
			);
			return;
		}

		$this->writeLine("Available commands:");

		$maxNameLength = 0;
		foreach($this->applicationCommandList as $command) {
			$nameLength = strlen($command->getName());
			if($nameLength > $maxNameLength) {
				$maxNameLength = $nameLength;
			}
		}

		foreach($this->applicationCommandList as $command) {
			$this->writeLine(" • " .
				str_pad($command->getName(), $maxNameLength, " ")
				. "\t"
				. $command->getDescription()
			);
		}

		$this->writeLine();
		$this->writeLine("Type gt help COMMAND to get help for that command");
	}
}