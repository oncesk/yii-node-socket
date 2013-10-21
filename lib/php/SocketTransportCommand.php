<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

class SocketTransportCommand extends CConsoleCommand {

	/**
	 * @var string
	 */
	public $logFile;

	/**
	 * @var string
	 */
	public $host = 'localhost';

	/**
	 * @var int
	 */
	public $port;

	/**
	 * @var string
	 */
	public $componentName = 'socketTransport';

	public function actionStart() {

	}

	public function actionStop() {

	}

	public function actionRestart() {

	}

	public function actionGetPid() {
		echo (int) $this->getPid() . "\n";
	}

	public function getHelp()
	{
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

	/**
	 * @return SocketTransport
	 */
	protected function getComponent() {
		$component = Yii::app()->getComponent($this->componentName);
		if (isset($component)) {
			return $component;
		}
		$this->usageError('Please provide valid socket transport component name like in config');
	}

	/**
	 * @param integer $pid
	 *
	 * @return int
	 */
	protected function writePid($pid) {
		return file_put_contents($this->getPidFile(), $pid);
	}

	/**
	 * @return int|null
	 */
	protected function getPid() {
		$pid = null;
		$pidFile = $this->getPidFile();
		if (file_exists($pidFile)) {
			$pid = (int) file_get_contents($pidFile);
		}
		return $pid;
	}

	/**
	 * @return string
	 */
	protected function getPidFile() {
		return Yii::getPathOfAlias('runtime') . DIRECTORY_SEPARATOR . $this->getComponent()->pidFile;
	}
}