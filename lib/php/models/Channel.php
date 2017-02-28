<?php
namespace YiiNodeSocket\Models;

class Channel extends AModel {

	const SOURCE_PHP = 1;
	const SOURCE_JAVASCRIPT = 2;
	const SOURCE_PHP_OR_JAVASCRIPT = 3;

	/**
	 * @var string unique channel name
	 */
	public $name;

	/**
	 * @var bool
	 */
	public $is_authentication_required = false;

	/**
	 * @var string separated by comma, can be array
	 */
	public $allowed_roles;

	/**
	 * @var integer
	 */
	public $subscriber_source = self::SOURCE_PHP;

	/**
	 * @var integer
	 */
	public $event_source = self::SOURCE_PHP;

	/**
	 * @var string|array
	 */
	public $properties;

	/**
	 * @var string
	 */
	public $create_date;

	/**
	 * @param string $class
	 *
	 * @return AModel
	 */
	public static function model($class = __CLASS__) {
		return parent::model($class);
	}

	/**
	 * @return array
	 */
	public function getSourceList() {
		return array(
			self::SOURCE_PHP,
			self::SOURCE_JAVASCRIPT,
			self::SOURCE_PHP_OR_JAVASCRIPT
		);
	}
               
	/**
	 * @return array
	 */
	public function rules() {
		return array_merge(parent::rules(), array(
			array(['name', 'is_authentication_required', 'subscriber_source', 'event_source'], 'required'),
			array('name', 'validateUniqueName'),
			array('name', 'string', 'min' => 2),
			array('properties', 'string', 'max' => 65000, 'skipOnEmpty' => true),
			array(['subscriber_source', 'event_source'], 'integer'),
			array(['subscriber_source', 'event_source'], 'in', 'range' => $this->getSourceList()),
			array('allowed_roles', 'string', 'min' => 1, 'skipOnEmpty' => true),
			array('create_date', 'safe')
		));
	}

	/**
	 * @param Subscriber $subscriber
	 * @param array      $subscribeOptions
	 *
	 * @return bool
	 */
	public function subscribe(Subscriber $subscriber, array $subscribeOptions = array()) {
		return SubscriberChannel::model()->createLink($this, $subscriber, $subscribeOptions);
	}

	/**
	 * @param Subscriber $subscriber
	 *
	 * @return bool
	 */
	public function unSubscribe(Subscriber $subscriber) {
		return SubscriberChannel::model()->destroyLink($this, $subscriber);
	}

	/**
	 * @param bool $refresh
	 *
	 * @return Subscriber[]
	 */
	public function getSubscribers($refresh = false) {
		return SubscriberChannel::model()->getSubscribers($this, $refresh);
	}

	/**
	 * @param string $name
	 *
	 * @return \YiiNodeSocket\Frames\Event
	 */
	public function createEvent($name = null) {
		$event = $this->getNodeSocket()->getFrameFactory()->createEventFrame();
		if ($name) {
			$event->setEventName($name);
		}
		$event->setChannel($this->name);
		return $event;
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array_merge(parent::attributeNames(), array(
			'name',
			'properties',
			'is_authentication_required',
			'allowed_roles',
			'subscriber_source',
			'event_source',
			'create_date'
		));
	}

	/**
	 * @return bool
	 */
	public function validateUniqueName() {
		if (!empty($this->name)) {
			$exists = $this->findByAttributes(array(
				'name' => $this->name
			));
			if ($exists) {
				if (!$this->getIsNewRecord() && $exists->id == $this->id) {
					return true;
				}
				$this->addError('name', 'Channel name should be unique');
				return false;
			}
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function beforeValidate() {
		if (is_array($this->properties) || is_object($this->properties)) {
			$this->properties = \yii\helpers\Json::encode($this->properties);
		} else if (!is_string($this->properties)) {
			$this->properties = '';
		}
		return parent::beforeValidate();
	}

	/**
	 * @return bool
	 */
	protected function beforeSave() {
		$this->is_authentication_required = (int) $this->is_authentication_required;
		if (is_array($this->allowed_roles)) {
			$this->allowed_roles = implode(',', $this->allowed_roles);
		} else if (!is_string($this->allowed_roles)) {
			$this->allowed_roles = '';
		} else {
			$this->allowed_roles = implode(',', array_map('trim', explode(',', $this->allowed_roles)));
		}
		return parent::beforeSave();
	}

	protected function afterLoad() {
		if ($this->properties) {
			$this->properties = \yii\helpers\Json::decode($this->properties);
		}
	}
}
