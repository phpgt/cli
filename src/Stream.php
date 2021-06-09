<?php
namespace Gt\Cli;

use SplFileObject;

class Stream {
	const IN = "in";
	const OUT = "out";
	const ERROR = "error";

	protected SplFileObject $error;
	protected SplFileObject $out;
	protected SplFileObject $in;
	protected SplFileObject $currentStream;

	public function __construct(
		string $in = null,
		string $out = null,
		string $error = null
	) {
		if(is_null($in)) {
			$in = "php://stdin";
		}
		if(is_null($out)) {
			$out = "php://stdout";
		}
		if(is_null($error)) {
			$error = "php://stderr";
		}

		$this->setStream($in, $out, $error);
	}

	public function setStream(string $in, string $out, string $error):void {
		$this->in = new SplFileObject(
			$in,
			"r"
		);
		$this->out = new SplFileObject(
			$out,
			"w"
		);
		$this->error = new SplFileObject(
			$error,
			"w"
		);
	}

	public function getInStream():SplFileObject {
		return $this->in;
	}

	public function getOutStream():SplFileObject {
		return $this->out;
	}

	public function getErrorStream():SplFileObject {
		return $this->error;
	}

	public function readLine(string $streamName = self::IN):string {
		$stream = $this->getNamedStream($streamName);
		$buffer = "";

		while(!strstr($buffer, "\n")) {
			$buffer .= $stream->fread(128);
			usleep(1_000);
		}

		return $buffer;
	}

	public function write(
		string $message,
		string $streamName = self::OUT
	):void {
		$this->getNamedStream($streamName)->fwrite($message);
	}

	public function writeLine(
		string $message = "",
		string $streamName = self::OUT
	):void {
		$this->write($message . PHP_EOL, $streamName);
	}

	protected function getNamedStream(string $streamName):SplFileObject {
		switch($streamName) {
		case self::IN:
			return $this->in;
		case self::OUT:
			return $this->out;
		case self::ERROR:
			return $this->error;
		}

		throw new InvalidStreamNameException($streamName);
	}
}
