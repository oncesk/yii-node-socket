<?php
namespace YiiNodeSocket\Model;

use YiiNodeSocket\Model\Driver\ADriver;

/**
 * Class AModel
 * @package YiiNodeSocket\Model
 */
abstract class AModel extends \CModel{

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var ADriver
	 */
	protected $_driver;

	/**
	 * @var boolean
	 */
	protected $_isNewRecord = true;

	public function __construct() {
		$this->id = uniqid(get_class($this));
		$this->afterConstruct();
	}

	/**
	 * @param ADriver $driver
	 *
	 * @return $this
	 */
	public function setDriver(ADriver $driver) {
		$this->_driver = $driver;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	public function save() {

	}

	public function update() {

	}

	public function delete() {

	}

	public function refresh() {

	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames() {
		return array(
			'id'
		);
	}


	/**
	 * @return array
	 * @throws \CException
	 */
	final public function toArray() {
		$data = $this->getDataForSave();
		if (!is_array($data)) {
			throw new \CException('AModel::getDataForSave should return array');
		}
		return $data;
	}

	/**
	 * @return array
	 */
	abstract public function getDataForSave();
}