<?php
namespace YiiNodeSocket\Console;


class WinConsole implements ConsoleInterface {

	public function __construct() {

	}

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
		echo "Start method not implemented in WinConsole class\n";
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