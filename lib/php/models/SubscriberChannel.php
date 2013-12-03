<?php
namespace YiiNodeSocket\Models;

use YiiNodeSocket\Frames\ChannelEvent;

class SubscriberChannel extends AModel {

	/**
	 * @var array
	 */
	protected static $_channelSubscribers = array();

	/**
	 * @var array
	 */
	protected static $_subscriberChannels = array();

	/**
	 * @var integer|string
	 */
	public $subscriber_id;

	/**
	 * @var integer|string
	 */
	public $channel_id;

	/**
	 * @var bool|integer
	 */
	public $can_send_event_from_php = true;

	/**
	 * @var bool|integer
	 */
	public $can_send_event_from_js = false;

	/**
	 * @var string|integer if string, will be converted to timestamp with strtotime function
	 */
	public $create_date;

	/**
	 * @param string $class
	 *
	 * @return SubscriberChannel
	 */
	public static function model($class = __CLASS__) {
		return parent::model($class);
	}

	/**
	 * @param Channel    $channel
	 * @param Subscriber $subscriber
	 * @param array      $options
	 *
	 * @return bool
	 */
	public static function createLink(Channel $channel, Subscriber $subscriber, array $options = array()) {
		//  maybe channel or subscriber not created
		if ($channel->getIsNewRecord() || $subscriber->getIsNewRecord()) {
			//  if yes, return false
			return false;
		}

		//  maybe link exists
		$link = self::model()->findByAttributes(array(
			'channel_id' => $channel->id,
			'subscriber_id' => $subscriber->id
		));

		if ($link) {
			//  add channel and subscriber to cache
			self::_addToCache($channel, $subscriber);
			//  if yes, try update options
			/* @var SubscriberChannel $link */
			if (!empty($options)) {
				$link->setOptions($options);
				return $link->save();
			}
			//  else return only true
			return true;
		}

		//  create link
		$link = new SubscriberChannel();
		$link->channel_id = $channel->id;
		$link->subscriber_id = $subscriber->id;
		$link->setOptions($options);
		if ($link->save()) {
			//  add to cache
			self::_addToCache($channel, $subscriber);
			return true;
		}
		return false;
	}

	/**
	 * @param Channel    $channel
	 * @param Subscriber $subscriber
	 *
	 * @return bool
	 */
	public static function destroyLink(Channel $channel, Subscriber $subscriber) {
		if ($channel->getIsNewRecord() || $subscriber->getIsNewRecord()) {
			return false;
		}
		$link = self::model()->findByAttributes(array(
			'channel_id' => $channel->id,
			'subscriber_id' => $subscriber->id
		));
		if ($link) {
			if ($link->delete()) {
				if (isset(self::$_channelSubscribers[$channel->id]) && isset(self::$_channelSubscribers[$channel->id][$subscriber->id])) {
					unset(self::$_channelSubscribers[$channel->id][$subscriber->id]);
				}
				if (isset(self::$_subscriberChannels[$subscriber->id]) && isset(self::$_subscriberChannels[$subscriber->id][$channel->id])) {
					unset(self::$_subscriberChannels[$subscriber->id][$channel->id]);
				}
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options = array()) {
		foreach (array('can_send_event_from_php', 'can_send_event_from_js') as $attribute) {
			if (array_key_exists($attribute, $options)) {
				$this->$attribute = $options[$attribute];
			}
		}
	}

	public function rules() {
		return array(
			array('subscriber_id, channel_id', 'required'),
			array('subscriber_id, channel_id', 'length', 'min' => 1, 'max' => 255),
			array('can_send_event_from_php, can_send_event_from_js', 'numerical', 'integerOnly' => true)
		);
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array(
			'subscriber_id',
			'channel_id',
			'can_send_event_from_php',
			'can_send_event_from_js',
			'create_date'
		);
	}

	/**
	 * @param Channel $channel
	 * @param bool    $refresh
	 *
	 * @return AModel[]
	 */
	public function getSubscribers(Channel $channel, $refresh = false) {
		if ($channel->getIsNewRecord()) {
			return array();
		}
		if (array_key_exists($channel->id, self::$_channelSubscribers) && !$refresh) {
			return self::$_channelSubscribers[$channel->id];
		}
		$links = SubscriberChannel::model()->findAllByAttributes(array(
			'channel_id' => $channel->id
		));
		$subscriberId = array();
		foreach ($links as $link) {
			$subscriberId[] = $link->subscriber_id;
		}
		$subscribers = Subscriber::model()->findAllByPk($subscriberId);
		foreach ($subscribers as $subscriber) {
			self::_addToCache($channel, $subscriber);
		}
		return $subscribers;
	}

	/**
	 * @param Subscriber $subscriber
	 * @param bool       $refresh
	 *
	 * @return AModel[]
	 */
	public function getChannels(Subscriber $subscriber, $refresh = false) {
		if ($subscriber->getIsNewRecord()) {
			return array();
		}
		if (array_key_exists($subscriber->id, self::$_subscriberChannels) && !$refresh) {
			return self::$_subscriberChannels[$subscriber->id];
		}
		$links = $this->findAllByAttributes(array(
			'subscriber_id' => $subscriber->id
		));
		$channelId = array();
		foreach ($links as $link) {
			$channelId[] = $link->id;
		}
		$channels = Channel::model()->findAllByPk($channelId);
		foreach ($channels as $channel) {
			self::_addToCache($channel, $subscriber);
		}
		return $channels;
	}

	/**
	 * @return bool
	 */
	protected function beforeValidate() {
		$this->can_send_event_from_js = (int) $this->can_send_event_from_js;
		$this->can_send_event_from_php = (int) $this->can_send_event_from_php;
		return parent::beforeValidate();
	}

	/**
	 * @param Channel    $channel
	 * @param Subscriber $subscriber
	 */
	private static function _addToCache(Channel $channel, Subscriber $subscriber) {
		if (!array_key_exists($channel->id, self::$_channelSubscribers)) {
			self::$_channelSubscribers[$channel->id] = array();
		}
		self::$_channelSubscribers[$channel->id][$subscriber->id] = $subscriber;
		if (!array_key_exists($subscriber->id, self::$_subscriberChannels)) {
			self::$_subscriberChannels[$subscriber->id] = array();
		}
		self::$_channelSubscribers[$subscriber->id][$channel->id] = $channel;
	}
}