<?php
namespace YiiNodeSocket\Frame;

class ChannelEvent extends Event {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_CHANNEL_EVENT;
	}

	/**
	 * @param $channelId
	 *
	 * @return ChannelEvent
	 */
	public function setChannel($channelId) {
		if ((is_string($channelId) || is_int($channelId)) && !empty($channelId)) {
			$this->addMetaData('channel', $channelId);
		}
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return false;
	}
}