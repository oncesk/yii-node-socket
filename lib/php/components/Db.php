<?php
namespace YiiNodeSocket\Components;

use YiiNodeSocket\Components\Db\DriverInterface;

class Db extends AComponent {

	/**
	 * @var string
	 */
	public $driver;

	/**
	 * @var array
	 */
	public $config = array();

	/**
	 * @var string
	 */
	protected $_driver;

	/**
	 * @return DriverInterface
	 */
	public function getDriver() {
		if ($this->_driver) {
			return $this->_driver;
		}
		return $this->_driver = $this->_createDriver();
	}

	/**
	 * @return array
	 */
	public function getConnectionOptions() {
		return array_merge($this->getDriver()->getConnectionOptions(), array(
			'driver' => $this->driver,
			'config' => $this->config
		));
	}

	/**
	 * @return DriverInterface
	 * @throws \CException
	 */
	protected function _createDriver() {
		if (!$this->driver) {
			throw new \CException('Invalid node socket db driver');
		}
		$class = '\YiiNodeSocket\Components\Db\\' . ucfirst($this->driver) . '\\' . ucfirst($this->driver) . 'Driver';
		$driver = new $class();
		/** @var \YiiNodeSocket\Components\Db\DriverInterface $driver */
		$driver->init($this->config);
		return $driver;
	}
}