<?php
namespace YiiNodeSocket\Component\Db;

use YiiNodeSocket\Model\AModel;

class MysqlDriver extends BaseDriver {

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function save(AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function delete(AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel[]
	 */
	public function findByAttributes(array $attributes, AModel $model) {
		return $this->callMethod(__METHOD__, $model);
	}

	protected function saveChannel() {

	}
}