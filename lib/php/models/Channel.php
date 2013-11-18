<?php
namespace YiiNodeSocket\Model;

class Channel extends AModel {

	const SUBSCRIBER_SOURCE_PHP = 1;
	const SUBSCRIBER_SOURCE_JAVASCRIPT = 2;
	const SUBSCRIBER_SOURCE_PHP_AND_JAVASCRIPT = 3;

	const EVENT_SOURCE_PHP = 1;
	const EVENT_SOURCE_JAVASCRIPT = 2;
	const EVENT_SOURCE_PHP_AND_JAVASCRIPT = 3;

	/**
	 * @var string|integer
	 */
	public $id;

	public $can_client_subscribe = true;

	/**
	 * if set to true nodejs server will be check client authentication, and if it not authenticated his
	 * will be discarded
	 *
	 * @var bool
	 */
	public $is_client_authentication_required = false;

	/**
	 * @var array
	 */
	public $allowed_client_roles = array();

	/**
	 * @var integer
	 */
	public $subscriber_source;

	/**
	 * @var integer
	 */
	public $event_source;

	/**
	 * @var string
	 */
	public $create_date;

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array(
			'id',
			'subscriber_source',
			'event_source',
			'create_date'
		);
	}

	/**
	 * @return Subscriber[]
	 */
	public function getSubscribers() {

	}

	/**
	 * Remove related objects
	 *
	 * @return mixed
	 */
	public function afterDelete() {

	}
}