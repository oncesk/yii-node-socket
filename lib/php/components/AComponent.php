<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 11/5/13
 * Time: 12:17 AM
 * To change this template use File | Settings | File Templates.
 */

namespace YiiNodeSocket\Component;


abstract class AComponent {

	/**
	 * @var \NodeSocket
	 */
	protected $_nodeSocket;

	public function __construct(\NodeSocket $nodeSocket, array $config = array()) {
		$this->_nodeSocket = $nodeSocket;

		$this->applyConfig($config);
	}

	protected function applyConfig(array $config) {
		foreach ($config as $k => $v) {
			$this->$k = $v;
		}
	}
}