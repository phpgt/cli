<?php
namespace Gt\Cli\Test;

use Gt\Cli\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase {
	public function testGetSetStream() {
		$tmp = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"cli",
		]);

		$inPath = implode(DIRECTORY_SEPARATOR, [$tmp, "in"]);
		$outPath = implode(DIRECTORY_SEPARATOR, [$tmp, "in"]);
		$errPath = implode(DIRECTORY_SEPARATOR, [$tmp, "in"]);
		touch($inPath);
		touch($outPath);
		touch($errPath);

		$stream = new Stream(
			$inPath,
			$outPath,
			$errPath
		);

		$in = $stream->getInStream();
		$out = $stream->getOutStream();
		$err = $stream->getErrorStream();

		self::assertEquals($inPath, $in->getRealPath());
		self::assertEquals($outPath, $out->getRealPath());
		self::assertEquals($errPath, $err->getRealPath());
	}
}