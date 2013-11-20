<?php
namespace YiiNodeSocket\Model\Driver;

use YiiNodeSocket\Component\AComponent;

require_once 'drivers/ADriver.php';
require_once 'drivers/FilesDriver.php';

class SubscriptionDriverManager extends AComponent {

	/**
	 * @var string
	 */
	public $type = 'file';

	/**
	 * @var array
	 */
	public $config = array();

	/**
	 * @var ADriver
	 */
	private $_driver;

	/**
	 * @return ADriver
	 */
	public function getDriver() {
		if ($this->_driver) {
			return $this->_driver;
		}
		return $this->_driver = $this->createDriver($this->type, $this->config);
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