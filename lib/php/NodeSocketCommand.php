<?php
namespace YiiNodeSocket;

use Yii;
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

class NodeSocketCommand extends \yii\console\Controller {

	/**
	 * @var string
	 */
	public $componentName = 'nodeSocket';

	/**
	 * @var string
	 */
	public $pathToNodeJs = 'node';

	/**
	 * @var int
	 */
	protected $_pid;

	/**
	 * @var \YiiNodeSocket\Console\ConsoleInterface
	 */
	private $_console;

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

	/**
	 * @param $error
	 */
	protected function usageError($error) {
		print "ERROR: " . $error . "\n";
		exit(1);
	}

	public function getHelp() {
		return <<<EOD
USAGE
  yiic node-socket [action] [parameter]

DESCRIPTION
  This command provides support for node socket extension

EXAMPLES
 * yiic node-socket start
   Start socket server

 * yiic node-socket stop
   Stop socket server

 * yiic node-socket restart
   Restart socket server

 * yiic node-socket getPid
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
		$server = implode(DIRECTORY_SEPARATOR, array(
			__DIR__,
			'..',
			'js',
			'server',
			'server.js'
		));
		return $this->pathToNodeJs . ' ' . $server;
	}

	/**
	 * @return string
	 */
	protected function getLogFile() {
		$logFile = $this->getComponent()->socketLogFile;
		if ($logFile) {
			return $logFile;
		}
		return \Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . 'socket-transport.server.log';
	}

	/**
	 * @return bool
	 */
	protected function isInProgress() {
		$pid = $this->getPid();
		if ($pid == 0) {
			return false;
		}
		return $this->getConsole()->isInProgress($pid);
	}

	/**
	 * @return NodeSocket
	 */
	protected function getComponent() {
		$nodeSocket = \Yii::$app->get($this->componentName);
                
		if ($nodeSocket) {
			return $nodeSocket;
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
		return \Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . $this->getComponent()->pidFile;
	}

	/**
	 * @return bool|int
	 */
	protected function _stop() {
		$pid = $this->getPid();
		if ($pid && $this->isInProgress()) {
			printf("Stopping socket server\n");
			$this->getConsole()->stopServer($this->getPid());
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
		$this->_pid = $this->getConsole()->startServer($this->makeCommand(), $this->getLogFile());
		if ($this->_pid) {
			printf("Server successfully started\n");
			return $this->writePid();
		}
		return false;
	}


	/**
	 * @return \YiiNodeSocket\Console\ConsoleInterface
	 */
	private function getConsole() {
		if ($this->_console) {
			return $this->_console;
		}
		if (strpos(PHP_OS, 'WIN') !== false) {
			$this->_console = new \YiiNodeSocket\Console\WinConsole();
		} else {
			$this->_console = new \YiiNodeSocket\Console\UnixConsole();
		}
		return $this->_console;
	}
}