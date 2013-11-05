<?php
namespace YiiNodeSocket\Component;

class Channel extends AComponent {

	const TYPE_PUBLIC = 1;
	const TYPE_PRIVATE = 2;

	const EVENT_SOURCE_PHP = 1;
	const EVENT_SOURCE_JS = 2;
	const EVENT_SOURCE_PHP_JS = 3;

	protected $_id;
	protected $_name;
	protected $_type;
	protected $_eventSource;
	protected $_owner;

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @return Subscriber[]
	 */
	public function getSubscribers() {

	}

	/**
	 * @param Subscriber $subscriber
	 *
	 * @return bool
	 */
	public function subscribe(Subscriber $subscriber) {

	}

	/**
	 * @param Subscriber $subscriber
	 *
	 * @return bool
	 */
	public function unSubscribe(Subscriber $subscriber) {

	}

	/**
	 * @return \YiiNodeSocket\Frame\Event
	 */
	public function event() {
		$event = $this->_nodeSocket->createEventFrame();
		if ($this->_id) {
		}
		return $event;
	}
}