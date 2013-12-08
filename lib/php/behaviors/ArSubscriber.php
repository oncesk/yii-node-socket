<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Components\ArEvent;
use YiiNodeSocket\Models\Subscriber;

/**
 * Class ArSubscriber
 * @package YiiNodeSocket\Behaviors
 */
class ArSubscriber extends ArBehavior {

	/**
	 * @var string
	 */
	public $role = 'user';

	/**
	 * @var string
	 */
	public $roleAttribute = 'role';

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
	 * @var Subscriber
	 */
	protected $_subscriber;

	/**
	 * @return Subscriber|null
	 */
	public function getSubscriber() {
		if ($this->_subscriber) {
			return $this->_subscriber;
		}
		if ($this->getOwner()->getIsNewRecord()) {
			return null;
		}
		return $this->_subscriber = Subscriber::model()->findByAttributes(array(
			'user_id' => $this->_getUserId()
		));
	}

	public function createSubscriber() {
		if (!$this->getSubscriber()) {
			$this->_subscriber = new Subscriber();
			$this->_subscriber->user_id = $this->_getUserId();
			$this->_subscriber->role = $this->getRole();

			$event = new ArEvent($this);
			$event->name = 'onSubscriberSave';
			$event->error = !$this->_subscriber->save();
			$this->triggerModelEvent($event);
		}
	}

	public function updateSubscriber() {
		if ($subscriber = $this->getSubscriber()) {
			$subscriber->role = $this->getRole();

			$event = new ArEvent($this);
			$event->name = 'onSubscriberSave';
			$event->error = !$subscriber->save();
			$this->triggerModelEvent($event);
		}
	}

	public function deleteSubscriber() {
		if ($subscriber = $this->getSubscriber()) {
			$event = new ArEvent($this);
			$event->name = 'onSubscriberDelete';
			$event->error = !$subscriber->delete();
			$this->triggerModelEvent($event);
		}
	}

	public function afterSave($event) {
		if ($this->getOwner()->getIsNewRecord()) {
			if ($this->createOnSave) {
				$this->createSubscriber();
			}
		} else {
			if ($this->updateOnSave) {
				$this->updateSubscriber();
			}
		}
	}

	public function afterDelete($event) {
		if ($this->deleteOnDelete) {
			$this->deleteSubscriber();
		}
	}

	/**
	 * @return mixed|null|string
	 */
	protected function getRole() {
		if ($this->roleAttribute && $this->getOwner()->hasAttribute($this->roleAttribute)) {
			return $this->getOwner()->getAttribute($this->roleAttribute);
		} else if ($this->role) {
			return $this->role;
		}
		return null;
	}

	/**
	 * @return mixed
	 */
	private function _getUserId() {
		return $this->getOwner()->getPrimaryKey();
	}
}