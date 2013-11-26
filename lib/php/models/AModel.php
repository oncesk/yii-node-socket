<?php
namespace YiiNodeSocket\Model;

use YiiNodeSocket\Component\Db\DriverInterface;
use YiiNodeSocket\Model\Driver\ADriver;

/**
 * Class AModel
 * @package YiiNodeSocket\Model
 */
abstract class AModel extends \CModel{

	/**
	 * @var DriverInterface
	 */
	public static $driver;

	/**
	 * @var \NodeSocket
	 */
	public static $nodeSocket;

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var boolean
	 */
	protected $_isNewRecord = true;

	/**
	 * @var AModel[]
	 */
	private static $_models = array();

	/**
	 * @param $class
	 *
	 * @return AModel
	 */
	public static function model($class) {
		if (array_key_exists($class, self::$_models)) {
			return self::$_models[$class];
		}
		return self::$_models[$class] = new $class();
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	/**
	 * @return bool
	 */
	public function save() {
		if ($this->beforeSave()) {
			$result = self::$driver->save($this);
			$this->afterSave();
			if ($result) {
				$this->_isNewRecord = false;
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
			$result = self::$driver->delete($this);
			$this->afterDelete();
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function refresh() {
		return self::$driver->refresh($this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel[]
	 */
	public function findAllByAttributes(array $attributes) {
		return self::$driver->findByAttributes($attributes, $this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel|null
	 */
	public function findByAttributes(array $attributes) {
		$result = $this->findAllByAttributes($attributes);
		if (!empty($result)) {
			return current($result);
		}
		return null;
	}

	/**
	 * @param string|int $pk
	 *
	 * @return AModel|null
	 */
	public function findByPk($pk) {
		if ($pk) {
			return $this->findByAttributes(array(
				'id' => $pk
			));
		}
		return null;
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
		$event = self::$nodeSocket->getFrameFactory()->createChannelEventFrame();
		$event
				->setAction('save.' . get_class($this))
				->setData($this->getAttributes())
				->send();
	}

	protected function beforeDelete() {
		return true;
	}

	protected function afterDelete() {
		$event = self::$nodeSocket->getFrameFactory()->createChannelEventFrame();
		$event
				->setAction('delete.' . get_class($this))
				->setData($this->getAttributes())
				->send();
	}
}