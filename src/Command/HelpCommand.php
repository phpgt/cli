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
	}

	public function run(ArgumentValueList $arguments): void {
		$this->output->writeLine($this->applicationName);
		$this->output->writeLine();

		if(empty($this->applicationCommandList)) {
			$this->output->writeLine(
				"There are no commands available"
			);
			return;
		}

		$this->output->writeLine("Available commands:");

		foreach($this->applicationCommandList as $command) {
			$this->output->writeLine(" • " .
				$command->getName()
				. "\t"
				. $command->getDescription()
			);
		}
	}
}