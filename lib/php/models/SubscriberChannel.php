<?php
namespace YiiNodeSocket\Model;

use YiiNodeSocket\Frame\ChannelEvent;

class SubscriberChannel extends AModel {

	public $subscriber_id;
	public $channel_id;
	public $can_send_event_from_php = true;
	public $can_send_event_from_js = false;
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
			array('subscription_id, channel_id, can_send_event_from_php, can_send_event_from_js', 'required'),
			array('subscription_id, channel_id', 'length', 'min' => 1, 'max' => 255),
			array('can_send_events_from_php, can_send_events_from_js', 'boolean')
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
}