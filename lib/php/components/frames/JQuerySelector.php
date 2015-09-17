<?php
namespace YiiNodeSocket\Components\Frames;

use YiiNodeSocket\Frames\JQuery;

class JQuerySelector {

	/**
	 * @var string
	 */
	protected $_selector;

	/**
	 * @var \YiiNodeSocket\Frames\JQuery
	 */
	protected $_owner;
	protected $_actions = array();
	protected $_scope;
	protected $_query = array();

	public function __construct($selector, JQuery $owner) {
		$this->_selector = $selector;
		$this->_owner = $owner;
		$this->_query = array(
			'selector' => $selector,
			'beforeApply' => null,
			'afterApply' => null,
			'actions' => array()
		);
	}

	/**
	 * @param string $data
	 *
	 * @return JQuerySelector
	 */
	public function append($data) {
		if (is_string($data) && !empty($data)) {
			$this->createAction('append', array($data));
		}
		return $this;
	}

	/**
	 * @param string $data
	 *
	 * @return JQuerySelector
	 */
	public function prepend($data) {
		if ($this->isValidString($data)) {
			$this->createAction('prepend', array($data));
		}
		return $this;
	}

	/**
	 * @param string $data
	 *
	 * @return JQuerySelector
	 */
	public function replaceWith($data) {
		if ($this->isValidString($data)) {
			$this->createAction('replaceWith', array($data));
		}
		return $this;
	}

	/**
	 * @param string $html
	 *
	 * @return JQuerySelector
	 */
	public function html($html) {
		if (is_string($html)) {
			$this->createAction('html', array($html));
		}
		return $this;
	}

	/**
	 * @param string $text
	 *
	 * @return JQuerySelector
	 */
	public function text($text) {
		if (is_string($text)) {
			$this->createAction('text', array($text));
		}
		return $this;
	}

	/**
	 * @param array $arguments
	 *
	 * @return JQuerySelector
	 */
	public function slideUp(array $arguments = array()) {
		return $this->createAction('slideUp', $arguments);
	}

	/**
	 * @param array $arguments
	 *
	 * @return JQuerySelector
	 */
	public function slideDown(array $arguments = array()) {
		return $this->createAction('slideDown', $arguments);
	}

	/**
	 * @param string $class
	 *
	 * @return JQuerySelector
	 */
	public function addClass($class) {
		if ($this->isValidString($class)) {
			$this->createAction('addClass', array($class));
		}
		return $this;
	}
        
        /**
	 * @param string $class
	 *
	 * @return JQuerySelector
	 */
	public function play() {		
                    $this->createAction('play');
		return $this;
	}

	/**
	 * @param string $class
	 *
	 * @return JQuerySelector
	 */
	public function removeClass($class) {
		if ($this->isValidString($class)) {
			$this->createAction('removeClass', array($class));
		}
		return $this;
	}

	/**
	 * @param string $selector
	 *
	 * @return JQuerySelector
	 */
	public function parents($selector) {
		if ($this->isValidString($selector)) {
			$this->createAction('parents', array($selector));
		}
		return $this;
	}

	/**
	 * @return JQuerySelector
	 */
	public function parent() {
		return $this->createAction('parent');
	}

	/**
	 * @return JQuerySelector
	 */
	public function children() {
		return $this->createAction('children');
	}

	/**
	 * @param string $selector
	 *
	 * @return JQuerySelector
	 */
	public function find($selector) {
		if ($this->isValidString($selector)) {
			$this->createAction('find', array($selector));
		}
		return $this;
	}

	/**
	 * @return JQuerySelector
	 */
	public function remove() {
		$this->createAction('remove');
		return $this;
	}
        
        /**
	 * @return JQuerySelector
	 */
	public function get() {
		$this->createAction('get');
		return $this;
	}

	/**
	 * @return JQuerySelector
	 */
	public function prev() {
		$this->createAction('prev');
		return $this;
	}

	/**
	 * @return JQuerySelector
	 */
	public function next() {
		$this->createAction('next');
		return $this;
	}

	/**
	 * @param string $name
	 * @param string|integer $value
	 *
	 * @return JQuerySelector
	 */
	public function attr($name, $value) {
		if ($this->isValidString($name) && !empty($value)) {
			$this->createAction('attr', array($name, $value));
		}
		return $this;
	}

	/**
	 * @param array $styles
	 *
	 * @return JQuerySelector
	 */
	public function css(array $styles) {
		return $this->createAction('css', array($styles));
	}

	/**
	 * @param array $arguments
	 * @return JQuerySelector
	 */
	public function show(array $arguments = array()) {
		$this->createAction('show', $arguments);
		return $this;
	}

	/**
	 * @param array $arguments
	 * @return JQuerySelector
	 */
	public function hide(array $arguments = array()) {
		return $this->createAction('hide', $arguments);
	}

	/**
	 * @param string $effect
	 * @param array $arguments
	 *
	 * @return JQuerySelector
	 */
	public function effect($effect, array $arguments = array()) {
		if ($this->isValidString($effect)) {
			array_unshift($arguments, $effect);
			$this->createAction('effect', $arguments);
		}
		return $this;
	}

	/**
	 * @return JQuerySelector
	 */
	public function getOwner() {
		return $this->_owner;
	}

	/**
	 * Set name of javascript function which will be called
	 * BEFORE apply action
	 *
	 * @param string $functionName
	 * @return JQuerySelector
	 */
	public function beforeApply($functionName) {
		if (is_string($functionName) && !empty($functionName)) {
			$this->_query['beforeApply'] = $functionName;
		}
		return $this;
	}

	/**
	 * Set name of javascript function which will be called
	 * AFTER apply action
	 *
	 * @param string $functionName
	 * @return JQuerySelector
	 */
	public function afterApply($functionName) {
		if (is_string($functionName) && !empty($functionName)) {
			$this->_query['afterApply'] = $functionName;
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getQuery() {
		return $this->_query;
	}

	/**
	 * @param $string
	 *
	 * @return bool
	 */
	private function isValidString($string) {
		return is_string($string) && !empty($string);
	}

	/**
	 * @param string $action
	 * @param array $arguments
	 *
	 * @return JQuerySelector
	 */
	private function createAction($action, array $arguments = array()) {
		$this->_query['actions'][] = array(
			'action' => $action,
			'arguments' => $arguments
		);
		return $this;
	}
}