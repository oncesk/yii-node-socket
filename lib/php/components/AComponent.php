<?php
namespace YiiNodeSocket\Components;

/**
 * Class AComponent
 *
 * @package YiiNodeSocket\Components
 */
abstract class AComponent {

	/**
	 * @var \NodeSocket
	 */
	protected $_nodeSocket;

	public function __construct(\YiiNodeSocket\NodeSocket $nodeSocket) {
		$this->_nodeSocket = $nodeSocket;
	}
}