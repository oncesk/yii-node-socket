<?php
namespace YiiNodeSocket\Console;

/**
 * Class WinConsole
 * @package YiiNodeSocket\Console
 */
class WinConsole implements ConsoleInterface {

	/**
	 * @param integer|string $pid
	 *
	 * @return boolean
	 */
	public function stopServer($pid) {
		// TODO: Implement stopServer() method.
	}

	/**
	 * @param string $command
	 * @param string $logFile
	 *
	 * @return integer pid
	 */
	public function startServer($command, $logFile) {
		echo "Start method is not implemented in WinConsole class\n";
		// TODO: Implement startServer() method.
		return false;
	}

	/**
	 * @param $pid
	 *
	 * @return boolean
	 */
	public function isInProgress($pid) {
		// TODO: Implement isInProgress() method.
	}
}