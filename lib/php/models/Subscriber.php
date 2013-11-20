<?php
namespace YiiNodeSocket\Model;

class Subscriber extends AModel {

	/**
	 * @var string
	 */
	public $role = 'guest';

	/**
	 * @var integer|null
	 */
	public $user_id;

	/**
	 * @var string
	 */
	public $sid;

	/**
	 * @var string
	 */
	public $sid_expiration;

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array_merge(parent::attributeNames(), array(
			'role',
			'user_id',
			'sid',
			'sid_expiration'
		));
	}

	/**
	 * @return Channel[]
	 */
	public function getChannels() {

	}

	/**
	 * @return SubscriberChannel[]
	 */
	public function getSubscriberChannel() {

	}
}