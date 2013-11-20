<?php
namespace YiiNodeSocket\Model;

class SubscriberChannel extends AModel {

	public $subscriber_id;
	public $channel_id;
	public $can_send_events_from_php = true;
	public $can_send_events_from_js = false;
	public $create_date;

	public function rules() {
		return array(
			array('subscription_id, channel_id, can_send_events_from_php, can_send_events_from_js', 'required'),
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
			'can_send_events_from_php',
			'can_send_events_from_js',
			'create_date'
		);
	}

	/**
	 * @return array
	 */
	public function getDataForSave() {
		$attributes = $this->getAttributes();
		$attributes['id'] = $this->getId();
		return $attributes;
	}


}