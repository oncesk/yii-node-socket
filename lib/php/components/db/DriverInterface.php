<?php
namespace YiiNodeSocket\Components\Db;

use YiiNodeSocket\Models\AModel;

interface DriverInterface {

	/**
	 * @param array $config
	 *
	 * @return void
	 */
	public function init(array $config);

	/**
	 * @return array
	 */
	public function getConnectionOptions();

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function save(AModel $model);

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function delete(AModel $model);

	/**
	 * @param AModel $model
	 *
	 * @return boolean
	 */
	public function refresh(AModel $model);

	/**
	 * @param        $pk
	 * @param AModel $model
	 *
	 * @return AModel|null
	 */
	public function findByPk($pk, AModel $model);

	/**
	 * @param array  $pk
	 * @param AModel $model
	 *
	 * @return mixed
	 */
	public function findAllByPk(array $pk, AModel $model);

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel
	 */
	public function findByAttributes(array $attributes, AModel $model);

	/**
	 * @param array  $attributes
	 * @param AModel $model
	 *
	 * @return AModel[]
	 */
	public function findAllByAttributes(array $attributes, AModel $model);
}