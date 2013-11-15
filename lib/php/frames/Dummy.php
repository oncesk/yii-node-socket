<?php
namespace YiiNodeSocket\Frame;

class Dummy extends AFrame {

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