<?php
namespace YiiNodeSocket\Frame;

class Dummy extends AFrame {

	public function __construct(\NodeSocket $nodeSocket = null) {}

	/**
	 * @return string
	 */
	public function getType() {
		return 'dummy';
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return false;
	}
}