<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 4:11 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class AFrame {

	const TYPE_EVENT = 'event';
	const TYPE_MULTIPLE_JAVASCRIPT_EXEC = 'exec';

	protected $_id;

	/**
	 * @var SocketTransport
	 */
	protected $_socketTransport;

	/**
	 * @var array
	 */
	protected $_container;

	/**
	 * @return string
	 */
	abstract public function getType();

	/**
	 * @param SocketTransport $socketTransport
	 */
	public function __construct(SocketTransport $socketTransport) {
		$this->_socketTransport = $socketTransport;
		$this->init();
		$this->createContainer();
	}

	public function getId() {
		if (isset($this->_id)) {
			return $this->_id;
		}
		list($micro, $time) = explode(' ', microtime());
		return $this->_id = $time + $micro;
	}

	public function send() {
		$this->emit();
	}

	/**
	 * @param mixed $data
	 *
	 * @return AFrame
	 */
	public function setData($data) {
		$this->_container['data'] = $data;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEncodedFrame() {
		return json_encode($this->_container);
	}

	/**
	 * @return array
	 */
	public function getFrame() {
		return $this->_container;
	}

	protected function emit() {
		$client = $this->createClient();
		$client->setHandshakeTimeout(2000);
		$client->init();
		$client
			->createFrame()
			->endPoint($this->_socketTransport->socketNamespace)
			->emit('socket.transport', array($this->getFrame()));
	}

	/**
	 * @return \ElephantIO\Client
	 */
	protected function createClient() {
		return new \ElephantIO\Client(
			sprintf('http://%s:%s', $this->_socketTransport->host, $this->_socketTransport->port),
			'socket.io',
			1,
			false
		);
	}

	protected function createContainer() {
		if (!isset($this->_container)) {
			$this->_container = array(
				'id' => $this->getId(),
				'type' => $this->getType(),
				'data' => array()
			);
		}
	}

	protected function init() {}
}