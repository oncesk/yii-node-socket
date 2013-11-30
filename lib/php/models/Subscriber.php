<?php
namespace YiiNodeSocket\Models;

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
	 * @return array
	 */
	public function rules() {
		return array_merge(parent::rules(), array(
			array('role', 'required'),
			array('role', 'length', 'min' => 1),
			array('user_id', 'numerical', 'integerOnly' => true),
			array('user_id, sid', 'validateUserIdOrSid'),
			array('sid', 'length', 'min' => 5, 'allowEmpty' => true),
			array('sid_expiration', 'numerical', 'integerOnly' => true, 'allowEmpty' => true)
		));
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
		return $this->_channels = $this->getDbDriver()->findByAttributes(array(
			'subscriber_id' => $this->id
		), Subscriber::model());
	}

	/**
	 * @param $attribute
	 *
	 * @return bool
	 */
	public function validateUserIdOrSid($attribute) {
		$value = $this->$attribute;
		if (empty($value)) {
			if ($attribute == 'sid' && empty($this->user_id) || ($attribute == 'user_id' && empty($this->sid))) {
				$this->addError($attribute, sprintf('User id or sid are required!'));
				return false;
			}
			$attribute = $attribute == 'sid' ? 'user_id' : 'sid';
		}
		//  check for unique
		$exists = $this->findByAttributes(array(
			$attribute => $this->$attribute
		));
		if ($exists) {
			if ($this->getIsNewRecord() || $exists->id != $this->id) {
				$this->addError($attribute, sprintf('%s should be unique!', $attribute));
				return false;
			}
		}
		return true;
	}

	/**
	 * @return bool
	 */
	protected function beforeValidate() {
		if ($this->sid_expiration && !is_numeric($this->sid_expiration)) {
			$this->sid_expiration = strtotime($this->sid_expiration);
		}
		return parent::beforeValidate();
	}
}