<?php
namespace YiiNodeSocket\Components\Db\Dummy;

use YiiNodeSocket\Components\Db\BaseDriver;
use YiiNodeSocket\Models\AModel;

class DummyDriver extends BaseDriver {

	/**
	 * @param array $config
	 *
	 * @return void
	 */
	public function init(array $config) {}

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel
	 */
	public function findByAttributes(array $attributes, AModel $model) {
		return null;
	}

	/**
	 * @param array  $pk
	 * @param AModel $model
	 *
	 * @return mixed
	 */
	public function findAllByPk(array $pk, AModel $model) {
		return array();
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function refresh(AModel $model) {
		return true;
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function save(AModel $model) {
		return true;
	}

	/**
	 * @return array
	 */
	public function getConnectionOptions() {
		return array();
	}

	/**
	 * @param        $pk
	 * @param AModel $model
	 *
	 * @return AModel|null
	 */
	public function findByPk($pk, AModel $model) {
		return null;
	}

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel[]
	 */
	public function findAllByAttributes(array $attributes, AModel $model) {
		return array();
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function delete(AModel $model) {
		return true;
	}
}