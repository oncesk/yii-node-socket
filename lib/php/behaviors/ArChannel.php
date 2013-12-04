<?php
namespace YiiNodeSocket\Behavior;

use YiiNodeSocket\Models\Channel;

class ArChannel extends ArBehavior {

	const ERROR_CAN_NOT_CREATE = 1;
	const ERROR_CAN_NOT_UPDATE = 2;
	const ERROR_CAN_NOT_DELETE = 3;

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
	 * @var string
	 */
	public $nodeSocketComponent = 'nodeSocket';

	/**
	 * @var bool
	 */
	public $createOnSave = true;

	/**
	 * @var bool
	 */
	public $updateOnSave = false;

	/**
	 * @var Channel
	 */
	protected $_channel;

	public function attach($owner) {
		parent::attach($owner);
		if (!\Yii::app()->hasComponent($this->nodeSocketComponent)) {
			throw new \CException('Node socket component not found');
		}
	}



	public function createChannel() {
		$this->_channel = new Channel();
		$this->attributesToChannel($this->_channel);
		$this->_channel->name = $this->getChannelName();
		if ($this->_channel->save()) {
			$event = new \CEvent($this);
			$this->onChannelCreate($event);
		} else {
			$this->onChannelError('Can not create', self::ERROR_CAN_NOT_CREATE, new \CEvent($this));
		}
	}

	public function updateChannel() {
		if ($channel = $this->getChannel()) {
			$this->attributesToChannel($channel);
			if ($this->_channel->save()) {
				$event = new \CEvent($this);
				$this->onChannelUpdate($event);
			} else {
				$this->onChannelError('Can not update', self::ERROR_CAN_NOT_UPDATE, new \CEvent($this));
			}
		}
	}

	public function deleteChannel() {
		if ($channel = $this->getChannel()) {
			if ($channel->delete()) {
				$event = new \CEvent($this);
				$this->onChannelDelete($event);
			} else {
				$this->onChannelError('Can not delete', self::ERROR_CAN_NOT_DELETE, new \CEvent($this));
			}
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
	 * @param \CEvent $event
	 */
	public function onChannelCreate(\CEvent $event) {
		$this->raiseEvent('onChannelCreate', $event);
	}

	/**
	 * @param \CEvent $event
	 */
	public function onChannelUpdate(\CEvent $event) {
		$this->raiseEvent('onChannelUpdate', $event);
	}

	/**
	 * @param \CEvent $event
	 */
	public function onChannelDelete(\CEvent $event) {
		$this->raiseEvent('onChannelDelete', $event);
	}

	/**
	 * @param string  $error
	 * @param integer $errorCode
	 * @param \CEvent $event
	 */
	public function onChannelError($error, $errorCode, \CEvent $event) {
		$event->params['error'] = $error;
		$event->params['errorCode'] = $errorCode;
		$this->raiseEvent('onChannelError', $event);
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
		$this->deleteChannel();
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