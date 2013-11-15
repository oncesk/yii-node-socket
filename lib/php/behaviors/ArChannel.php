<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 11/4/13
 * Time: 11:07 PM
 * To change this template use File | Settings | File Templates.
 */

namespace YiiNodeSocket\Behavior;

use YiiNodeSocket\Component\Channel;
use YiiNodeSocket\Component\Subscriber;

class ArChannel extends ArChannelEvent {

	/**
	 * @param \CModel $model
	 *
	 * @return bool
	 */
	public function subscribe(\CModel $model) {
		if (!$this->getOwner()->getIsNewRecord()) {
			$subscriber = $this->_getSubscriberFromModel($model);
			if (!$subscriber) {
				return false;
			}
			if ($channel = $this->getChannel()) {
				return $channel->subscribe($subscriber);
			}
		}
		return false;
	}

	/**
	 * @param \CModel $model
	 *
	 * @return bool
	 */
	public function unSubscribe(\CModel $model) {
		if (!$this->getOwner()->getIsNewRecord()) {
			$subscriber = $this->_getSubscriberFromModel($model);
			if (!$subscriber) {
				return false;
			}
			if ($channel = $this->getChannel()) {
				return $channel->unSubscribe($subscriber);
			}
		}
		return false;
	}

	/**
	 * @param \CModel $model
	 *
	 * @return null|boolean
	 */
	public function haveSubscriber(\CModel $model) {
		if (!$this->getOwner()->getIsNewRecord()) {

		}
		return null;
	}

	/**
	 * @return string|null
	 */
	public function channelId() {
		if (!$this->getOwner()->getIsNewRecord()) {
			return 'channel:' . get_class($this->getOwner()) . ':' . $this->getOwner()->{$this->getOwner()->getPrimaryKey()};
		}
		return null;
	}

	/**
	 * @return Channel
	 */
	public function getChannel() {

	}

	/**
	 * @param \CModel $model
	 *
	 * @return Subscriber|null
	 */
	protected function _getSubscriberFromModel(\CModel $model) {

	}
}