<?php
namespace Gt\Cli\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;

class VersionCommand extends Command {
	protected string $script;

	public function __construct(string $script) {
		$this->script = $script;
	}

	public function run(ArgumentValueList $arguments = null):void {
		$this->writeLine(
			$this->getVersion($arguments->get(
				"command"
			)
		));
	}

	public function getName():string {
		return "version";
	}

	public function getDescription():string {
		return "Get the version of the application";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [];
	}

	protected function getVersion(string $command = null):string {
		$version = "Version number not found";

		$nestedDirectoryCount = 25;
		$dir = __DIR__;
		do {
			$dir = dirname($dir);
			$files = scandir($dir);
			$nestedDirectoryCount--;
		}
		while(!in_array("vendor", $files)
		|| $nestedDirectoryCount < 1);

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

		$scriptName = $command ?? $this->script;
		$scriptName = pathinfo($scriptName, PATHINFO_FILENAME);

		foreach($installed as $item) {
			$binArray = $item->bin ?? [];
			foreach($binArray as $bin) {
				$bin = pathinfo($bin, PATHINFO_FILENAME);
				if($bin === $scriptName) {
					$version = $item->version;
					break;
				}
			}
		}

		return $version;
	}
}
