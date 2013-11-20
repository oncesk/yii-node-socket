<?php
namespace YiiNodeSocket\Model\Driver;

use YiiNodeSocket\Model\AModel;
use YiiNodeSocket\Model\Channel;
use YiiNodeSocket\Model\Subscriber;
use YiiNodeSocket\Model\SubscriberChannel;

abstract class ADriver {

	/**
	 * @var array
	 */
	private $_loaded = array();

	/**
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		foreach ($config as $k => $v) {
			$this->$k = $v;
		}

		$this->afterConstruct();
	}

	/**
	 * @param AModel $model
	 *
	 * @return bool
	 */
	abstract public function save(AModel $model);

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	abstract public function update(AModel $model);

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	abstract public function delete(AModel $model);

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	abstract public function refresh(AModel $model);

	/**
	 * @param string $id
	 * @param AModel $model
	 *
	 * @return AModel
	 */
	public function findByPk($id, AModel $model) {
		$class = get_class($model);
		if (isset($this->_loaded[$class]) && isset($this->_loaded[$class][$id])) {

		}
	}

	/**
	 * @param string $attribute
	 * @param string|integer $attributeValue
	 * @param string $modelClass
	 *
	 * @return array
	 */
	abstract public function findByAttribute($attribute, $attributeValue, $modelClass);

	protected function getObject()

	/**
	 * @param AModel $model
	 */
	protected function saveObject(AModel $model) {
		$this->_loaded[get_class($model)][$model->getId()] = $model;
	}
}