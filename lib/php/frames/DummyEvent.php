<?php
namespace YiiNodeSocket\Frame;

class DummyEvent extends Dummy {

	/**
	 * @param $eventName
	 *
	 * @return DummyEvent
	 */
	public function setEventName($eventName) {
		return $this;
	}

	/**
	 * @param string $room
	 *
	 * @return DummyEvent
	 */
	public function setRoom($room) {
		return $this;
	}

	/**
	 * @param string $channel
	 *
	 * @return DummyEvent
	 */
	public function setChannel($channel) {
		return $this;
	}
}