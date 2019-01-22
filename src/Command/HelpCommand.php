<?php
namespace Gt\Cli\Command;

use Gt\Cli\Argument\ArgumentValueList;

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
		$this->setName("help");
		$this->setDescription("Display information about available commands");

		$this->applicationName = $applicationName;
		$this->applicationCommandList = $applicationCommandList;
		$this->applicationCommandList []= $this;
	}

	public function run(ArgumentValueList $arguments = null): void {
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
	}
}