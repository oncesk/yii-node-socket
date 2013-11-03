<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

class NodeSocketCommand extends CConsoleCommand {

	/**
	 * @var string
	 */
	public $componentName = 'nodeSocket';

	/**
	 * @var int
	 */
	protected $_pid;

	/**
	 * @var string
	 */
	protected $_command = 'node %s';

	/**
	 * @var string
	 */
	protected $_stopCommand = 'kill %s';

	public function actionStart() {
		if (!$this->_start()) {
			$this->usageError("Cannot start server");
		}
		exit(1);
	}

	public function actionStop() {
		if (!$this->_stop()) {
			exit(1);
		}
	}

	public function actionRestart() {
		if ($this->_stop()) {
			if (!$this->_start()) {
				$this->usageError("Cannot start server");
			}
			exit(1);
		} else {
			$this->usageError('Cannot stop server');
		}
	}

	public function actionGetPid() {
		echo (int)$this->getPid() . "\n";
	}

	public function getHelp() {
		return <<<EOD
USAGE
  yiic socketTransport [action] [parameter]

DESCRIPTION
  This command provides support for database migrations. The optional
  'action' parameter specifies which specific migration task to perform.
  It can take these values: up, down, to, create, history, new, mark.
  If the 'action' parameter is not given, it defaults to 'up'.
  Each action takes different parameters. Their usage can be found in
  the following examples.

EXAMPLES
 * yiic socketTransport start
   Start socket server

 * yiic socketTransport stop
   Stop socket server

 * yiic socketTransport restart
   Restart socket server

 * yiic socketTransport getPid
   Display socket pid
EOD;
	}

	protected function compileServer() {
		printf("Compile server\n");
		$nodeSocket = $this->getComponent();
		ob_start();
		include __DIR__ . '/../js/server/server.config.js.php';
		$js = ob_get_clean();
		return file_put_contents(__DIR__ . '/../js/server/server.config.js', $js);
	}

	protected function compileClient() {
		printf("Compile client\n");
		$nodeSocket = $this->getComponent();
		ob_start();
		include __DIR__ . '/../js/client/client.template.js';
		$js = ob_get_clean();
		return file_put_contents(__DIR__ . '/../js/client/client.js', $js);
	}

	/**
	 * @return string
	 */
	protected function makeCommand() {
		$serverDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'nodejs' . DIRECTORY_SEPARATOR;
		return sprintf($this->_command,
			$serverDir . 'server.js'
		);
	}

	/**
	 * @return string
	 */
	protected function getLogFile() {
		$logFile = $this->getComponent()->socketLogFile;
		if ($logFile) {
			return $logFile;
		}
		return Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'socket-transport.server.log';
	}

	/**
	 * @return bool
	 */
	protected function isInProgress() {
		$pid = $this->getPid();
		if ($pid == 0) {
			return false;
		}
		$command = 'ps -p ' . $pid;
		exec($command,$op);
		if (!isset($op[1])) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return NodeSocket
	 */
	protected function getComponent() {
		$component = Yii::app()->getComponent($this->componentName);
		if (isset($component)) {
			return $component;
		}
		$this->usageError('Please provide valid socket transport component name like in config');
	}

	/**
	 * @return int
	 */
	protected function writePid() {
		printf("Update pid in file %s\n", $this->getPidFile());
		return file_put_contents($this->getPidFile(), $this->getPid());
	}

	/**
	 * @return int|null
	 */
	protected function getPid($update = false) {
		if (isset($this->_pid) && !$update) {
			return $this->_pid;
		}
		if ($update || !isset($this->_pid)) {
			$this->updatePid();
		}
		return $this->_pid;
	}

	/**
	 * Update process pid
	 */
	protected function updatePid() {
		$this->_pid = 0;
		$pidFile = $this->getPidFile();
		if (file_exists($pidFile)) {
			$this->_pid = (int)file_get_contents($pidFile);
		}
	}

	/**
	 * @return string
	 */
	protected function getPidFile() {
		return Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . $this->getComponent()->pidFile;
	}

	/**
	 * @return bool|int
	 */
	protected function _stop() {
		$pid = $this->getPid();
		if ($pid && $this->isInProgress()) {
			printf("Stopping socket server\n");
			$command = sprintf($this->_stopCommand, $this->getPid());
			exec($command);
			if (!$this->isInProgress()) {
				printf("Server successfully stopped\n");
				$this->_pid = 0;
				return $this->writePid();
			}
			printf("Stopping server error\n");
			return false;
		}
		printf("Server is stopped\n");
		return true;
	}

	/**
	 * @return bool|int
	 */
	protected function _start() {
		if ($this->getPid() && $this->isInProgress()) {
			printf("Server already started\n");
			return true;
		}
		$this->compileServer();
		$this->compileClient();
		printf("Starting server\n");
		$command = 'nohup ' . $this->makeCommand() . ' > ' . $this->getLogFile() . ' 2>&1 & echo $!';
		exec($command, $op);
		$this->_pid = (int) $op[0];
		if ($this->_pid) {
			printf("Server successfully started\n");
			return $this->writePid();
		}
		return false;
	}
}