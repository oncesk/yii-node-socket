<?php
namespace YiiNodeSocket\Component\Db;

use YiiNodeSocket\Model\AModel;

/**
 * Class BaseDriver
 * @package YiiNodeSocket\Component\Db
 */
abstract class BaseDriver implements DriverInterface {

	/**
	 * @param array $config
	 *
	 * @return void
	 */
	public function init(array $config) {
		foreach ($config as $k => $v) {
			$this->$k = $v;
		}
	}

	/**
	 * @param AModel $model
	 *
	 * @return string
	 */
	protected function resolveModelName(AModel $model) {
		return get_class($model);
	}

	/**
	 * @param string $method
	 * @param AModel $model
	 *
	 * @return null
	 */
	protected function callMethod($method, AModel $model) {
		$method = $method . $this->resolveModelName($model);
		if (method_exists($this, $method)) {
			return $this->$method($model);
		}
		return null;
	}
}