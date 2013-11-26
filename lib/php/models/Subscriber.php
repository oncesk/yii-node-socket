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
	 * @var Channel[]
	 */
	private $_channels;

	/**
	 * @param string $class
	 *
	 * @return AModel
	 */
	public static function model($class = __CLASS__) {
		return parent::model($class);
	}

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
	 * @param bool $refresh
	 *
	 * @return Channel[]
	 */
	public function getChannels($refresh = false) {
		if ($this->_channels && !$refresh) {
			return $this->_channels;
		}
		return $this->_channels = self::$driver->findByAttributes(array(
			'subscriber_id' => $this->id
		), Subscriber::model());
	}
}