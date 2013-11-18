<?php
namespace YiiNodeSocket\Model\Driver;

abstract class ADriver implements DriverInterface {

	/**
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		foreach ($config as $k => $v) {
			$this->$k = $v;
		}

		$this->afterConstruct();
	}

	protected function afterConstruct() {}
}