<?php
namespace YiiNodeSocket\Component\Frame;

use YiiNodeSocket\Frame\JSelector;

class JSelectorQuery {

	protected $_selector;
	protected $_owner;
	protected $_actions = array();

	public function __construct($selector, $owner = null) {
		$this->_selector = $selector;
		$this->_owner = $owner;
	}

	/**
	 * @param string $data
	 *
	 * @return JSelectorQuery
	 */
	public function append($data) {
		if (is_string($data) && !empty($data)) {
			$this->_actions[] = $this->_createAction('append')->arguments(array(
				$data
			));
		}
		return $this;
	}

	public function prepend($data) {

	}

	public function replaceWith($data) {

	}

	public function html($html) {
	}

	public function text() {

	}

	public function slideUp(array $arguments = array()) {

	}

	public function slideDown(array $arguments = array()) {

	}

	public function addClass($class) {

	}

	public function removeClass($class) {
		
	}

	/**
	 * @return JSelectorQuery
	 */
	public function remove() {
		$this->_actions[] = $this->_createAction('remove');
		return $this;
	}

	/**
	 * @param array $arguments
	 * @return JSelectorQuery
	 */
	public function show(array $arguments = array()) {
		$this->_actions[] = $this->_createAction('show')->arguments($arguments);
		return $this;
	}

	/**
	 * @param array $arguments
	 * @return JSelectorQuery
	 */
	public function hide(array $arguments = array()) {
		$this->_actions[] = $this->_createAction('hide')->arguments($arguments);
		return $this;
	}

	/**
	 * @return JSelector
	 */
	public function getOwner() {
		return $this->_owner;
	}

	/**
	 * @param string $action
	 *
	 * @return JSelectorQueryAction
	 */
	private function _createAction($action) {
		return new JSelectorQueryAction($action);
	}

	/**
	 * @param $string
	 *
	 * @return bool
	 */
	private function isValidString($string) {
		return is_string($string) && !empty($string);
	}
}

class JSelectorQueryAction {

	private $_action = array();

	/**
	 * @param string $action
	 */
	public function __construct($action) {
		$this->_action['name'] = $action;
	}

	/**
	 * @param array $arguments
	 */
	public function arguments(array $arguments) {
		$this->_action['args'] = $arguments;
		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return $this->_action;
	}
}