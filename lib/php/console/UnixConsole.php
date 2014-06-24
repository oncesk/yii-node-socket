<?php
namespace YiiNodeSocket\Console;

/**
 * Class UnixConsole
 * @package YiiNodeSocket\Console
 */
class UnixConsole implements ConsoleInterface {

	/**
	 * @param int|string $pid
	 */
	public function stopServer($pid) {
		exec('kill ' . $pid);
	}

	/**
	 * We need start server and write server logs into $logFile
	 *
	 * @param string $command
	 * @param string $logFile
	 *
	 * @return integer pid
	 */
	public function startServer($command, $logFile) {
		$command = 'nohup ' . $command . ' > ' . $logFile . ' 2>&1 & echo $!';
		exec($command, $op);
		return (int) $op[0];
	}

	/**
	 * Check if process with pid in progress
	 *
	 * @param $pid
	 *
	 * @return boolean
	 */
	public function isInProgress($pid) {
		$command = 'ps -p ' . $pid;
		exec($command,$op);
		if (!isset($op[1])) {
			return false;
		} else {
			return true;
		}
	}
}