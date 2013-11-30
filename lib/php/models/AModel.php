<?php
namespace YiiNodeSocket\Models;

use YiiNodeSocket\Components\Db\DriverInterface;

/**
 * Class AModel
 * @package YiiNodeSocket\Models
 */
abstract class AModel extends \CModel{

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var boolean
	 */
	private $_isNewRecord;

	/**
	 * @var AModel[]
	 */
	private static $_models = array();

	/**
	 * @param string $class
	 *
	 * @return AModel
	 */
	public static function model($class) {
		if (array_key_exists($class, self::$_models)) {
			return self::$_models[$class];
		}
		return self::$_models[$class] = new $class(null);
	}

	/**
	 * @param string $scenario
	 */
	public function __construct($scenario = 'insert') {
		if ($scenario === null) {
			return;
		}

		$this->_isNewRecord = ($scenario == 'insert');
	}

	/**
	 * @param string $scenario
	 *
	 * @return AModel
	 */
	final public function newInstance($scenario = 'insert') {
		$class = get_class($this);
		$model = new $class($scenario);
		return $model;
	}

	/**
	 * @return \NodeSocket
	 */
	public function getNodeSocket() {
		return \Yii::app()->nodeSocket;
	}

	/**
	 * @return DriverInterface
	 */
	public function getDbDriver() {
		return $this->getNodeSocket()->getDb()->getDriver();
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	public function rules() {
		return array(
			array('id', 'length', 'allowEmpty' => true),
			array('id', 'safe')
		);
	}

	/**
	 * @return bool
	 */
	public function save() {
		if ($this->beforeSave()) {
			$result = false;
			if ($this->validate()) {
				$result = $this->getDbDriver()->save($this);
				$this->afterSave();
				if ($result) {
					$this->_isNewRecord = false;
				}
			}
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function delete() {
		if ($this->beforeDelete()) {
			$result = $this->getDbDriver()->delete($this);
			$this->afterDelete();
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function refresh() {
		return $this->getDbDriver()->refresh($this);
	}

	/**
	 * @param $pk
	 *
	 * @return null|AModel
	 */
	public function findByPk($pk) {
		return $this->getDbDriver()->findByPk($pk, $this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel[]
	 */
	public function findAllByAttributes(array $attributes) {
		return $this->getDbDriver()->findAllByAttributes($attributes, $this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel
	 */
	public function findByAttributes(array $attributes) {
		return $this->getDbDriver()->findByAttributes($attributes, $this);
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array(
			'id'
		);
	}

	protected function beforeSave() {
		return true;
	}

	protected function afterSave() {
		$event = $this->getNodeSocket()->getFrameFactory()->createChannelEventFrame();
		$event
				->setAction('save.' . get_class($this))
				->setData($this->getAttributes())
				->send();
	}

	protected function beforeDelete() {
		return true;
	}

	protected function afterDelete() {
		$event = $this->getNodeSocket()->getFrameFactory()->createChannelEventFrame();
		$event
				->setAction('delete.' . get_class($this))
				->setData($this->getAttributes())
				->send();
	}
}