<?php
namespace YiiNodeSocket\Frame;

/**
 * todo implement setters and getters
 *
 * Class RuntimeServerConfiguration
 * @package YiiNodeSocket\Frame
 */
class RuntimeServerConfiguration extends AFrame {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_RUNTIME_CONFIGURATION;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return false;
	}
}