<?php
class EventFrame extends AFrame {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_EVENT;
	}

	/**
	 * @param $eventName
	 *
	 * @return EventFrame
	 */
	public function setEventName($eventName) {
		if (is_string($eventName) && !empty($eventName)) {
			$this->_container['event'] = $eventName;
		}
		return $this;
	}

	protected function createContainer() {
		parent::createContainer();
		$this->_container['event'] = 'undefined';
	}
}