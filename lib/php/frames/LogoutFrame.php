<?php
namespace YiiNodeSocket\Frames;

class LogoutFrame extends Authentication {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_LOGOUT;
	}
}