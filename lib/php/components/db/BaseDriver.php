<?php
namespace YiiNodeSocket\Components\Db;

use YiiNodeSocket\Models\AModel;

/**
 * Class BaseDriver
 * @package YiiNodeSocket\Components\Db
 */
abstract class BaseDriver implements DriverInterface {

	/**
	 * @param AModel $model
	 *
	 * @return string
	 */
	protected function resolveModelName(AModel $model) {
		$class = get_class($model);
		if (strpos($class, '\\')) {
			return substr($class, strrpos($class, '\\') + 1);
		}
		return $class;
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