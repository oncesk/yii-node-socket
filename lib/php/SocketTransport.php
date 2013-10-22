<?php
require_once 'frames/AFrame.php';
require_once 'frames/EventFrame.php';
require_once 'frames/JsExecFrame.php';

class SocketTransport extends CApplicationComponent {

	/**
	 * @var string
	 */
	public $host = '127.0.0.1';

	/**
	 * @var int
	 */
	public $port = 3001;

	/**
	 * Default is runtime/socket-transport.server.log
	 *
	 * @var string
	 */
	public $socketLogFile;

	/**
	 * @var string
	 */
	public $socketNamespace = '/';

	/**
	 * @var string
	 */
	public $pidFile = 'socket-transport.pid';

	/**
	 * @var string
	 */
	protected $_assetUrl;

	public function init() {
		parent::init();
		require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
			'..',
			'vendor',
			'elephant.io',
			'lib',
			'ElephantIO',
			'Client.php'
		));
	}

	/**
	 * @return EventFrame
	 */
	public function createEventFrame() {
		return new EventFrame($this);
	}

	/**
	 * @return JsExecFrame
	 */
	public function createJSExecFrame() {
		return new JsExecFrame($this);
	}

	/**
	 * @return bool
	 */
	public function registerClientScripts() {
		if ($this->_assetUrl) {
			return true;
		}
		$this->_assetUrl = Yii::app()->assetManager->publish(__DIR__ . '/../javascript');
		if ($this->_assetUrl) {
			Yii::app()->clientScript->registerScriptFile($this->_assetUrl . '/client.js');
			return true;
		}
		return false;
	}
}