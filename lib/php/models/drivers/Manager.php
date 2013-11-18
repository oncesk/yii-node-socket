<?php
namespace YiiNodeSocket\Model\Driver;

class Manager {

	/**
	 * @var string
	 */
	public static $driverType = 'file';

	/**
	 * @var array
	 */
	public static $driverConfiguration = array();

	/**
	 * @var DriverInterface
	 */
	private static $_driver;

	/**
	 * @return ADriver|DriverInterface
	 */
	public static function getDriver() {
		if (isset(self::$_driver)) {
			return self::$_driver;
		}
		return self::$_driver = self::createDriver(self::$driverType, self::$driverConfiguration);
	}

	/**
	 * @param string $driverType
	 * @param array  $config
	 *
	 * @return ADriver
	 */
	private static function createDriver($driverType, array $config) {
		$class = ucfirst(strtolower($driverType)) . 'Driver';
		return new $class($config);
	}
}