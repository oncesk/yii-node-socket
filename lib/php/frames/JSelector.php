<?php
namespace YiiNodeSocket\Frame;

use YiiNodeSocket\Component\Frame\JSelectorQuery;

class JSelector extends Event {

	/**
	 * @var JSelectorQuery[]
	 */
	private $_selectors;

	/**
	 * @param string $selector
	 * @return JSelectorQuery
	 */
	public function find($selector) {
		if (array_key_exists($selector, $this->_selectors)) {
			return $this->_selectors[$selector];
		}
		return $this->_selectors[$selector] = new JSelectorQuery($selector, $this);
	}

	/**
	 * Set name of javascript function which will be called
	 * BEFORE apply action
	 *
	 * @param string $functionName
	 * @return JSelector
	 */
	public function beforeAction($functionName) {
		if (is_string($functionName) && !empty($functionName)) {
			$this->addMetaData('before', $functionName);
		}
		return $this;
	}

	/**
	 * Set name of javascript function which will be called
	 * AFTER apply action
	 *
	 * @param string $functionName
	 * @return JSelector
	 */
	public function afterAction($functionName) {
		if (is_string($functionName) && !empty($functionName)) {
			$this->addMetaData('after', $functionName);
		}
		return $this;
	}
}