<?php
namespace YiiSocketTransport\Frame;

/**
 * Class Invoke
 * @package YiiSocketTransport\Frame
 */
class Invoke extends Event {

	protected $_functions = array();
	protected $_methods = array();

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_INVOKE;
	}

	/**
	 * @param string $functionName
	 * @param array $arguments
	 * @param null|string $scope
	 *
	 * @return $this
	 */
	public function invokeFunction($functionName, array $arguments = array(), $scope = null) {
		if (is_string($functionName) && !empty($functionName)) {
			$this->_functions[] = array(
				'function' => $functionName,
				'arguments' => $arguments,
				'scope' => is_string($scope) ? $scope : null
			);
		}
		return $this;
	}

	/**
	 * @param string $object
	 * @param string $method
	 * @param array  $arguments
	 * @param string|null $scope
	 */
	public function invokeMethodOfObject($object, $method, array $arguments = array(), $scope = null) {
		if (is_string($object) && is_string($method) && !empty($object) && !empty($method)) {
			$this->_methods[] = array(
				'object' => $object,
				'method' => $method,
				'arguments' => $arguments,
				'scope' => is_string($scope) ? $scope : null
			);
		}
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return !empty($this->_functions) || !empty($this->_methods);
	}

	/**
	 * @return bool
	 */
	protected function beforeSend() {
		$this->setData(array(
			'functions' => $this->_functions,
			'methods' => $this->_methods
		));
		return true;
	}
}