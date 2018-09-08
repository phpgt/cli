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
		$this->stream->writeLine($this->applicationName);
		$this->stream->writeLine();

		$this->stream->writeLine("Available commands:");

		foreach($this->applicationCommandList as $command) {
			$this->stream->writeLine(" • " .
				$command->getName()
				. "\t"
				. $command->getDescription()
			);
		}
	}
}