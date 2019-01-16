<?php
namespace Gt\Cli\Test;

use Gt\Cli\InvalidStreamNameException;
use Gt\Cli\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase {
	public function testGetSetStream() {
		$tmp = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"cli",
		]);
		if(!is_dir($tmp)) {
			mkdir($tmp, 0775, true);
		}

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

		chdir($tmp);
		foreach(scandir($tmp) as $file) {
			if($file[0] === ".") {
				continue;
			}

			unlink($file);
		}
		rmdir($tmp);
	}

	public function testWrite() {
		$stream = new Stream(
			"php://memory",
			"php://memory",
			"php://memory"
		);
		$out = $stream->getOutStream();
		$stream->write("test");
		$out->rewind();
		self::assertEquals("test", $out->fread(1024));
	}

	public function testWriteLine() {
		$stream = new Stream(
			"php://memory",
			"php://memory",
			"php://memory"
		);
		$out = $stream->getOutStream();
		$stream->writeLine("test");
		$out->rewind();
		self::assertRegExp("/^test\r?\n$/", $out->fread(1024));
	}

	public function testWriteToError() {
		$stream = new Stream(
			"php://memory",
			"php://memory",
			"php://memory"
		);
		$out = $stream->getOutStream();
		$err = $stream->getErrorStream();

		$stream->write("this should go to error", Stream::ERROR);
		$out->rewind();
		$err->rewind();
		self::assertEmpty($out->fread(1024));
		self::assertEquals("this should go to error", $err->fread(1024));
	}

	public function testWriteToIn() {
		$stream = new Stream(
			"php://memory",
			"php://memory",
			"php://memory"
		);
		$in = $stream->getInStream();

		$stream->write("can't write to stdin", Stream::IN);
		$in->rewind();
		self::assertEmpty($in->fread(1024));
	}

	public function testInvalidStreamName() {
		$stream = new Stream(
			"php://memory",
			"php://memory",
			"php://memory"
		);
		$this->expectException(InvalidStreamNameException::class);
		$stream->write("this does not exist", "nothing");
	}
}