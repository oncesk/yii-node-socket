<?php
namespace YiiNodeSocket\Behavior;

use YiiNodeSocket\Component\Channel;
use YiiNodeSocket\Component\Subscriber;

class ArSubscriber extends ArBehavior {

	/**
	 * @return Channel[]
	 */
	public function getChannels() {

	}

	/**
	 * @return null|string
	 */
	public function getSubscriberId() {
		if ($this->getOwner()->getIsNewRecord()) {
			return null;
		}
		$pk = $this->getOwner()->getPrimaryKey();
		if (is_array($pk)) {
			$pk = md5(\CJSON::encode($pk));
		}
		if ($pk) {
			return 'subscriber:' . get_class($this->getOwner()) . ':' . $this->getOwner()->getPrimaryKey();
		}
		return null;

	}

	/**
	 * @return Subscriber
	 */
	public function getSubscriber() {

	}
}