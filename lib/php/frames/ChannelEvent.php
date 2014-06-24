<?php
namespace YiiNodeSocket\Frames;

class ChannelEvent extends Event {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_CHANNEL_EVENT;
	}

	/**
	 * @param string $action
	 *
	 * @return ChannelEvent
	 */
	public function setAction($action) {
		if ((is_string($action) || is_int($action)) && !empty($action)) {
			$this->addMetaData('action', $action);
		}
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->hasMetaData('action');
	}
}