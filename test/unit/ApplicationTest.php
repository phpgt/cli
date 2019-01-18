<?php
namespace Gt\Cli\Test;

use Gt\Cli\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase {
	public function testSetStream() {
		$tmp = implode(DIRECTORY_SEPARATOR, [
			sys_get_temp_dir(),
			"phpgt",
			"cli",
		]);
		if(!is_dir($tmp)) {
			mkdir($tmp, 0775, true);
		}

		$inPath = implode(DIRECTORY_SEPARATOR, [$tmp, "in"]);
		$outPath = implode(DIRECTORY_SEPARATOR, [$tmp, "out"]);
		$errPath = implode(DIRECTORY_SEPARATOR, [$tmp, "err"]);
		touch($inPath);
		touch($outPath);
		touch($errPath);

		$application = new Application("test-app");
		$application->setStream($inPath, $outPath, $errPath);
		$application->run();

		$out = file_get_contents($outPath);
		$err = file_get_contents($errPath);
		self::assertContains("Application received no arguments", $err);
		self::assertEmpty($out);

		$application = null;
		unlink($inPath);
		unlink($outPath);
		unlink($errPath);
	}
}