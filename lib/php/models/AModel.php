<?php
namespace YiiNodeSocket\Model;

use YiiNodeSocket\Model\Driver\DriverInterface;

/**
 * Class AModel
 * @package YiiNodeSocket\Model
 */
abstract class AModel extends \CModel{

	/**
	 * @var boolean
	 */
	protected $_isNewRecord = true;

	public function __construct() {
		$this->afterConstruct();
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	/**
	 * Remove related objects
	 *
	 * @return mixed
	 */
	abstract public function afterDelete();
}