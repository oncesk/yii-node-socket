<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Components\ArEvent;
use YiiNodeSocket\Models\Channel;

class ArChannel extends ArBehavior {

	/**
	 * @var string if set in javascript you can catch events for this alias
	 */
	public $alias;

	/**
	 * @var array list of model attributes which can be accessed from javascript
	 */
	public $properties = array();

	/**
	 * @var array
	 */
	public $attributes;

	/**
	 * @var bool
	 */
	public $createOnSave = true;

	/**
	 * @var bool
	 */
	public $updateOnSave = false;

	/**
	 * @var bool
	 */
	public $deleteOnDelete = true;

	/**
	 * @var Channel
	 */
	protected $_channel;

	/**
	 * @param \CActiveRecord $model
	 *
	 * @return bool
	 */
	public function subscribe(\CActiveRecord $model) {
		try {
			if ($channel = $this->getChannel()) {
				return $channel->subscribe($model->getSubscriber());
			}
		} catch (\CException $e) {}
		return false;
	}

	/**
	 * @param \CActiveRecord $model
	 *
	 * @return bool
	 */
	public function unSubscriber(\CActiveRecord $model) {
		try {
			if ($channel = $this->getChannel()) {
				return $channel->unSubscribe($model->getSubscriber());
			}
		} catch(\CException $e) {}
		return false;
	}

	public function createChannel() {
		$this->_channel = new Channel();
		$this->attributesToChannel($this->_channel);
		$this->_channel->name = $this->getChannelName();

		$event = new ArEvent($this);
		$event->name = 'onChannelSave';
		$event->error = !$this->_channel->save();
		$this->triggerModelEvent($event);
	}

	public function updateChannel() {
		if ($channel = $this->getChannel()) {
			$this->attributesToChannel($channel);

			$event = new ArEvent($this);
			$event->name = 'onChannelSave';
			$event->error = !$channel->save();
			$this->triggerModelEvent($event);
		}
	}

	public function deleteChannel() {
		if ($channel = $this->getChannel()) {
			$event = new ArEvent($this);
			$event->name = 'onChannelDelete';
			$event->error = !$channel->delete();
			$this->triggerModelEvent($event);
		}
	}

	/**
	 * @return Channel
	 */
	public function getChannel() {
		if (isset($this->_channel)) {
			return $this->_channel;
		}
		$this->_channel = Channel::model()->findByAttributes(array(
			'name' => $this->getChannelName()
		));
		return $this->_channel;
	}

	/**
	 * @param \CModelEvent $event
	 */
	protected function afterSave(\CModelEvent $event) {
		if ($this->getOwner()->getIsNewRecord() && $this->createOnSave) {
			$this->createChannel();
		} else if (!$this->getOwner()->getIsNewRecord() && $this->updateOnSave) {
			$this->updateChannel();
		}
	}

	/**
	 * @param \CModelEvent $event
	 */
	protected function afterDelete(\CModelEvent $event) {
		if ($this->deleteOnDelete) {
			$this->deleteChannel();
		}
	}

	/**
	 * @param Channel $channel
	 */
	protected function attributesToChannel(Channel $channel) {
		foreach ($this->attributes as $attribute) {
			if ($attribute != 'name') {
				$channel->$attribute = $this->getOwner()->getAttribute($attribute);
			}
		}
	}

	/**
	 * @return string
	 */
	protected function getChannelName() {
		$pk = $this->getOwner()->getPrimaryKey();
		if (is_array($pk)) {
			$pk = md5(\CJSON::encode($pk));
		}
		return get_class($this->getOwner()) . ':' . $pk;
	}
}