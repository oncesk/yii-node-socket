<?php
namespace YiiNodeSocket\Frame;

class DummyEvent extends Dummy {

	public $event;
	public $room;
	public $channel;

	/**
	 * @param $eventName
	 *
	 * @return DummyEvent
	 */
	public function setEventName($eventName) {
		$this->event = $eventName;
		return $this;
	}

	/**
	 * @param string $room
	 *
	 * @return DummyEvent
	 */
	public function setRoom($room) {
		$this->room = $room;
		return $this;
	}

	/**
	 * @param string $channel
	 *
	 * @return DummyEvent
	 */
	public function setChannel($channel) {
		$this->channel = $channel;
		return $this;
	}
}