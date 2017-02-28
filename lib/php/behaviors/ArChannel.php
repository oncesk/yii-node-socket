<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Components\ArEvent;
use YiiNodeSocket\Models\Channel;

/**
 *
 * Class ArChannel
 * @package YiiNodeSocket\Behaviors
 */
class ArChannel extends ArBehavior {

	/**
	 * @var string if set in javascript you can catch events for this alias
	 */
	public $alias;

	/**
	 * @var array list of model (owner) attributes which will be fetched in channel properties
	 */
	public $properties = array();

	/**
	 * @var array
	 */
	public $attributes = array();

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
	public function unSubscribe(\CActiveRecord $model) {
		try {
			if ($channel = $this->getChannel()) {
				return $channel->unSubscribe($model->getSubscriber());
			}
		} catch(\CException $e) {}
		return false;
	}

	/**
	 * @param string $name
	 *
	 * @return \YiiNodeSocket\Frames\Event|null
	 */
	public function createEvent($name) {
		if ($channel = $this->getChannel()) {
			return $channel->createEvent($name);
		}
		return null;
	}

	public function createChannel() {
		$this->_channel = new Channel();
		$this->attributesToChannel($this->_channel);
		$this->_channel->name = $this->getChannelName();

		$event = new ArEvent();
                $event->sender = $this;
		$event->name = 'onChannelSave';
		$event->error = !$this->_channel->save();
		$this->triggerModelEvent($event);
	}

	public function updateChannel() {
		if ($channel = $this->getChannel()) {
			$this->attributesToChannel($channel);

			$event = new ArEvent();
                        $event->sender = $this;
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
		$channel = Channel::model()->findByAttributes(array(
			'name' => $this->getChannelName()
		));
                $this->_channel = $channel;
		return $this->_channel;
	}

	/**
	 * @return string
	 */
	public function getAlias() {
		if ($this->alias) {
			return $this->alias;
		}
		return $this->alias = strtolower(get_class($this->getOwner()));
	}

	/**
	 * @param \CModelEvent $event
	 */
	public function afterSave($event) {
		if ($this->getOwner()->getIsNewRecord() && $this->createOnSave) {
			$this->createChannel();
		} else if (!$this->getOwner()->getIsNewRecord() && $this->updateOnSave) {
			$this->updateChannel();
		}
	}

	/**
	 * @param \CModelEvent $event
	 */
	public function afterDelete($event) {
		if ($this->deleteOnDelete) {
			$this->deleteChannel();
		}
	}

	/**
	 * @param Channel $channel
	 */
	protected function attributesToChannel(Channel $channel) {
		foreach ($this->attributes as $attribute => $value) {
			if ($attribute != 'name') {
				$channel->$attribute = $value;
			}
		}
		$properties = $this->getOwner()->getAttributes($this->properties);
		$properties['alias'] = $this->getAlias();
		$channel->properties = $properties;
	}

	/**
	 * @return string
	 */
	protected function getChannelName() {
		$pk = $this->getOwner()->getPrimaryKey();
		if (is_array($pk)) {
			$pk = md5(\yii\helpers\Json::encode($pk));
		}
		return $pk;//get_class($this->getOwner()) . ':' . $pk;
	}
}