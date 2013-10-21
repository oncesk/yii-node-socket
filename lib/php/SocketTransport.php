<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 3:08 PM
 * To change this template use File | Settings | File Templates.
 */

class SocketTransport extends CApplicationComponent {

	/**
	 * @var string
	 */
	public $host;

	/**
	 * @var int
	 */
	public $port;

	/**
	 * See https://github.com/oncesk/yii-elephant.io-component
	 *
	 * @var string
	 */
	public $elephantIOComponentName = '';

	/**
	 * @var bool
	 */
	public $socketEnableLogger = true;

	/**
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
}