<?php
namespace YiiNodeSocket\Model;

class SubscriberChannel extends AModel {

	public $id;
	public $subscriber_id;
	public $channel_id;
	public $create_date;

	public function rules() {
		return array(
			array('subscription_id, channel_id', 'required'),
			array('subscription_id, channel_id', 'length', 'min' => 1, 'max' => 255)
		);
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array(
			'id',
			'subscriber_id',
			'channel_id',
			'create_date'
		);
	}
}