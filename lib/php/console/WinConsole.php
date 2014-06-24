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
		$command = 'taskkill /f /PID ' . $pid;
        	exec($command);
	}

	/**
	 * @param string $command
	 * @param string $logFile
	 *
	 * @return integer pid
	 */
	public function startServer($command, $logFile) {
		$command = 'start /b ' . $command . ' > ' . $logFile;
	        $process = proc_open($command, array(array("pipe", "r"), array("pipe", "w"), array("pipe", "w")), $pipes);
	        $status = proc_get_status($process);
	        $pid = $status['pid'];
	        if ($pid) {
	            return $this->parseProccess($pid);
	        } else {
	            return false;
	        }
	}

	/**
	 * @param $pid
	 *
	 * @return boolean
	 */
	public function isInProgress($pid) {
		$pidCompare = $this->parseProccess($pid);
	        if($pidCompare === $pid) {
	            return true;
	        }
	        else {
	            return false;
	        }
	}
	
	protected function parseProccess($pid) {
	        $output = array_filter(explode(" ", shell_exec("wmic process get parentprocessid,processid | find \"$pid\"")));
	        array_pop($output);
	        return (int)end($output);
	}
	
}
