<?php
require_once 'frames/IFrameFactory.php';
require_once 'frames/FrameFactory.php';

use YiiSocketTransport\Frame\IFrameFactory;

class SocketTransport extends CApplicationComponent implements IFrameFactory {

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
	public $serverNamespace = '/server';

	/**
	 * @var string
	 */
	public $clientNamespace = '/client';

	/**
	 * @var string
	 */
	public $pidFile = 'socket-transport.pid';

	/**
	 * @var int timeout for handshaking in miliseconds
	 */
	public $handshakeTimeout = 2000;

	/**
	 * @var bool
	 */
	public $allowClientSubscribe = true;

	/**
	 * @var bool
	 */
	public $allowClientUnSubscribe = true;

	/**
	 * @var string
	 */
	protected $_assetUrl;

	/**
	 * @var \YiiSocketTransport\Frame\FrameFactory
	 */
	protected $_frameFactory;

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
		$this->_frameFactory = new \YiiSocketTransport\Frame\FrameFactory($this);
	}

	/**
	 * @return YiiSocketTransport\Frame\Event
	 */
	public function createEventFrame() {
		return $this->_frameFactory->createEventFrame();
	}

	/**
	 * @return \YiiSocketTransport\Frame\ChannelEvent
	 */
	public function createChannelEventFrame() {
		return $this->_frameFactory->createChannelEventFrame();
	}

	/**
	 * @return \YiiSocketTransport\Frame\Multiple
	 */
	public function createMultipleFrame() {
		return $this->_frameFactory->createMultipleFrame();
	}

	/**
	 * @return \YiiSocketTransport\Frame\PublicData
	 */
	public function createPublicDataFrame() {
		return $this->_frameFactory->createPublicDataFrame();
	}

	/**
	 * @return \YiiSocketTransport\Frame\VolatileRoomEvent
	 */
	public function createVolatileRoomEventFrame() {
		return $this->_frameFactory->createVolatileRoomEventFrame();
	}

	/**
	 * @return bool
	 */
	public function registerClientScripts() {
		if ($this->_assetUrl) {
			return true;
		}
		$this->_assetUrl = Yii::app()->assetManager->publish(__DIR__ . '/../js/client');
		if ($this->_assetUrl) {
			Yii::app()->clientScript->registerScriptFile(sprintf("http://%s:%d%s", $this->host, $this->port, '/socket.io/socket.io.js'));
			Yii::app()->clientScript->registerScriptFile($this->_assetUrl . '/client.js');
			return true;
		}
		return false;
	}
}