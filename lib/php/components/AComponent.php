<?php
namespace YiiNodeSocket\Component;

/**
 * Class AComponent
 *
 * @package YiiNodeSocket\Component
 */
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