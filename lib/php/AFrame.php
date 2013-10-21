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
		if (Yii::app()->hasComponent($this->_socketTransport->elephantIOComponentName)) {
			$component = Yii::app()->getComponent($this->_socketTransport->elephantIOComponentName);
			if (!($component instanceof YiiElephantIOComponent)) {
				throw new CException('For sending frame to socket your need connect yii-elephant.io-component. See https://github.com/oncesk/yii-elephant.io-component');
			}
			$component->emit('/', 'anything', $this->encodeFrame());
		}
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
	public function encodeFrame() {
		return json_encode($this->_container);
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