<?php
namespace Gt\Cli\Process;

use Gt\Cli\Stream;

class Process {
	const OS_WINDOWS = "windows";
	const OS_UNIX = "unix";

	protected $os;
	protected $command;
	protected $stream;
	protected $pid;

	public function __construct(string $command) {
		$this->command = $command;
		$this->detectOS();
		$this->setupStreams();
	}

	public function exec():void {
		$command = $this->command;

		switch($this->os) {
		case self::OS_WINDOWS:
			$command .= "";
			break;

		case self::OS_UNIX:
			$command .= " 1> {$this->stream[Stream::OUT]}"
				. " 2> {$this->stream[Stream::ERROR]}"
				. " &; echo $!";
			break;
		}

		$this->pid = -1;

		exec($command, $output);
		var_dump($output);die();
	}

	public function isAlive():bool {
		switch($this->os) {
		case self::OS_WINDOWS:
			break;

		case self::OS_UNIX:
			$return = exec("kill -0 {$this->pid}");
// $return will contain "No such process" if the pid is not alive.
			if(strlen(trim($return)) === 0) {
				return true;
			}
			else {
				return false;
			}
			break;
		}
	}

	public function kill():void {
		switch($this->os) {
		case self::OS_WINDOWS:
			break;

		case self::OS_UNIX:
			exec("kill {$this->pid}");
			break;
		}
	}

	public function read(string $streamName = Stream::OUT):string {
		$content = "";
		$stream = $this->stream[$streamName];

		while(!feof($stream)) {
			$content .= fread($stream, 1024);
		}

		return $content;
	}

	protected function detectOS():void {
// TODO: Git bash and CMD.exe are both backslashes... When exec is used within
// Git bash, does the subprocess execute in bash or CMD?
		if(DIRECTORY_SEPARATOR === "\\") {
			$this->os = self::OS_WINDOWS;
		}
		else {
			$this->os = self::OS_UNIX;
		}
	}

	protected function setupStreams():void {
		$id = uniqid();
		$tmp = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"cli",
			"proc",
		]);
		if(!is_dir($tmp)) {
			mkdir($tmp, 0775, true);
		}

		$this->stream = [];
		foreach([Stream::OUT, Stream::ERROR] as $stream) {
			$pathName = implode(DIRECTORY_SEPARATOR, [
				$tmp,
				"$id.$stream",
			]);

			touch($pathName);
			$this->stream[$stream] = fopen($pathName, "r");
		}
	}
}