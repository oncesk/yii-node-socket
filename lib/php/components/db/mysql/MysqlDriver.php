<?php
namespace YiiNodeSocket\Component\Db\Mysql;

use YiiNodeSocket\Component\Db\BaseDriver;
use YiiNodeSocket\Model\AModel;
use YiiNodeSocket\Model\Channel;
use YiiNodeSocket\Model\Subscriber;
use YiiNodeSocket\Model\SubscriberChannel;

class MysqlDriver extends BaseDriver {

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function save(AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function refresh(AModel $model) {
		return true;
	}


	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function delete(AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel[]
	 */
	public function findByAttributes(array $attributes, AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	protected function saveChannel(Channel $channel) {

	}

	protected function saveSubscriber(Subscriber $subscriber) {

	}

	protected function saveSubscriberChannel(SubscriberChannel $subscriberChannel) {

	}
}