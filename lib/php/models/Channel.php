<?php
namespace YiiNodeSocket\Model;

class Channel extends AModel {

	const SUBSCRIBER_SOURCE_PHP = 1;
	const SUBSCRIBER_SOURCE_JAVASCRIPT = 2;
	const SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT = 3;

	const EVENT_SOURCE_PHP = 1;
	const EVENT_SOURCE_JAVASCRIPT = 2;
	const EVENT_SOURCE_PHP_OR_JAVASCRIPT = 3;

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
	public $subscriber_source = self::EVENT_SOURCE_PHP_OR_JAVASCRIPT;

	/**
	 * @var integer
	 */
	public $event_source = self::EVENT_SOURCE_PHP;

	/**
	 * @var string
	 */
	public $create_date;

	/**
	 * @var Subscriber[]
	 */
	private $_subscribers;

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
	public function rules() {
		return array(
			array('name, is_authentication_required, subscriber_source, event_source', 'required'),
			array('subscriber_source, event_source', 'numerical', 'integerOnly' => true),
			array('allowed_roles', 'length', 'min' => 1, 'allowEmpty' => true),
			array('create_date', 'safe')
		);
	}

	/**
	 * @param Subscriber $subscriber
	 * @param array      $subscribeOptions
	 *
	 * @return bool
	 */
	public function subscribe(Subscriber $subscriber, array $subscribeOptions = array()) {
		if ($this->getIsNewRecord() || $subscriber->getIsNewRecord()) {
			return false;
		}
		$subscriberChannel = SubscriberChannel::model()->findByAttributes(array(
			'channel_id' => $this->id,
			'subscriber_id' => $subscriber->id
		));
		if ($subscriberChannel) {
			return true;
		}
		$subscriberChannel = new SubscriberChannel();
		$subscriberChannel->setOptions($subscribeOptions);
		$subscriberChannel->subscriber_id = $subscriber->id;
		$subscriberChannel->channel_id = $this->id;
		if ($subscriberChannel->save()) {
			if ($this->_subscribers) {
				$this->_subscribers[] = $subscriber;
			}
			return true;
		}
		return false;
	}

	/**
	 * @param Subscriber $subscriber
	 *
	 * @return bool
	 */
	public function unSubscribe(Subscriber $subscriber) {
		if ($this->getIsNewRecord() || $subscriber->getIsNewRecord()) {
			return true;
		}
		$subscriberChannel = SubscriberChannel::model()->findByAttributes(array(
			'channel_id' => $this->id,
			'subscriber_id' => $subscriber->id
		));
		if ($subscriberChannel) {
			return $subscriberChannel->delete();
		}
		return true;
	}

	/**
	 * @param bool $refresh
	 *
	 * @return Subscriber[]
	 */
	public function getSubscribers($refresh = false) {
		if ($this->_subscribers && !$refresh) {
			return $this->_subscribers;
		}
		return $this->_subscribers = self::$driver->findByAttributes(array(
			'channel_id' => $this->id
		), Subscriber::model());
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array_merge(parent::attributeNames(), array(
			'name',
			'is_authentication_required',
			'allowed_roles',
			'subscriber_source',
			'event_source',
			'create_date'
		));
	}
}