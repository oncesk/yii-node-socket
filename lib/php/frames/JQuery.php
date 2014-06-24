<?php
namespace YiiNodeSocket\Frames;

require_once __DIR__ . '/../components/frames/JQuerySelector.php';
use YiiNodeSocket\Components\Frames\JQuerySelector;

class JQuery extends Event {

	/**
	 * @var JQuerySelector[]
	 */
	private $_queries;

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_JQUERY;
	}

	/**
	 * @param $selector
	 *
	 * @return JQuerySelector
	 * @throws \CException
	 */
	public function createQuery($selector) {
		if (!is_string($selector) || empty($selector)) {
			throw new \CException('Selector should be a not empty string');
		}
		$query = new JQuerySelector($selector, $this);
		$this->_queries[] = $query;
		return $query;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return !empty($this->_queries);
	}

	public function prepareFrame() {
		$data = array();
		foreach ($this->_queries as $query) {
			$data[] = $query->getQuery();
		}
		$this->setData($data);
	}
}