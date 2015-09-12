<?php
namespace YiiNodeSocket\Models;

use YiiNodeSocket\Components\Db\DriverInterface;

/**
 * Class AModel
 * @package YiiNodeSocket\Models
 */
abstract class AModel extends \yii\base\Model{

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var boolean
	 */
	private $_isNewRecord;

	/**
	 * @var AModel[]
	 */
	private static $_models = array();

	/**
	 * @param string $class
	 *
	 * @return AModel
	 */
	public static function model($class) {
		if (array_key_exists($class, self::$_models)) {
			return self::$_models[$class];
		}
		return self::$_models[$class] = new $class(null);
	}

	/**
	 * @param string $scenario
	 */
	public function __construct($scenario = 'insert') {
		if ($scenario === null) {
			return;
		}

		$this->_isNewRecord = ($scenario == 'insert');
		$this->init();
                
//		$this->afterConstruct();
                
	}

	/**
	 * @param string $scenario
	 *
	 * @return AModel
	 */
	final public function newInstance($scenario = 'insert') {
		$class = get_class($this);
		$model = new $class($scenario);
		return $model;
	}

	/**
	 * @param array $attributes
	 */
	final public function load(array $attributes) {
		if ($this->beforeLoad($attributes)) {
			$this->setAttributes($attributes);
			$this->afterLoad();
		}
	}

	public function init() {}

	/**
	 * @return \NodeSocket
	 */
	public function getNodeSocket() {
		return \Yii::$app->nodeSocket;
	}

	/**
	 * @return DriverInterface
	 */
	public function getDbDriver() {
		return $this->getNodeSocket()->getDb()->getDriver();
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord() {
		return $this->_isNewRecord;
	}

	/**
	 * @param \yii\base\ModelEvent $event
	 */
	public function onBeforeSave(\yii\base\ModelEvent $event) {
		$this->trigger('onBeforeSave', $event);
	}

	/**
	 * @param \yii\base\ModelEvent $event
	 */
	public function onAfterSave(\yii\base\ModelEvent $event) {
		$this->trigger('onAfterSave', $event);
	}

	/**
	 * @param \yii\base\ModelEvent $event
	 */
	public function onBeforeDelete(\yii\base\ModelEvent $event) {
		$this->trigger('onBeforeDelete', $event);
	}

	/**
	 * @param \yii\base\ModelEvent $event
	 */
	public function onAfterDelete(\yii\base\ModelEvent $event) {
		$this->trigger('onAfterDelete', $event);
	}

	/**
	 * @return array
	 */
	public function rules() {
		return array(
			array('id', 'string', 'skipOnEmpty' => true),
			array('id', 'safe')
		);
	}

	/**
	 * @return bool
	 */
	public function save() {
		if ($this->beforeSave()) {
			$result = false;
			if ($this->validate()) {
				$result = $this->getDbDriver()->save($this);
				$this->afterSave();
				if ($result) {
					$this->_isNewRecord = false;
				}
			}
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function delete() {
		if ($this->beforeDelete()) {
			$result = $this->getDbDriver()->delete($this);
			if ($result) {
				$this->afterDelete();
			}
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function refresh() {
		return $this->getDbDriver()->refresh($this);
	}

	/**
	 * @param int $pk
	 *
	 * @return null|AModel
	 */
	public function findByPk($pk) {
		return $this->getDbDriver()->findByPk($pk, $this);
	}

	/**
	 * @param array $pk
	 *
	 * @return AModel[]
	 */
	public function findAllByPk(array $pk) {
		return $this->getDbDriver()->findAllByPk($pk, $this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel[]
	 */
	public function findAllByAttributes(array $attributes) {
		return $this->getDbDriver()->findAllByAttributes($attributes, $this);
	}

	/**
	 * @param array $attributes
	 *
	 * @return AModel
	 */
	public function findByAttributes(array $attributes) {
		return $this->getDbDriver()->findByAttributes($attributes, $this);
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

	protected function beforeSave() {
		$event = new \yii\base\ModelEvent();
                $event->sender = $this;
		$this->onBeforeSave($event);
		return $event->isValid;
	}

	protected function afterSave() {
		$modelEvent = new \yii\base\ModelEvent();
                $modelEvent->sender = $this;
		$this->onAfterSave($modelEvent);
		if ($modelEvent->isValid) {
			$event = $this->getNodeSocket()->getFrameFactory()->createChannelEventFrame();
			$event
					->setAction('save.' . get_class($this))
					->setData($this->getAttributes())
					->send();
		}
	}

	protected function beforeDelete() {
		$event = new \yii\base\ModelEvent();
                $event->sender = $this;
		$this->onBeforeDelete($event);
		return $event->isValid;
	}

	protected function afterDelete() {
		$modelEvent = new \yii\base\ModelEvent();
                $modelEvent->sender = $this;
		$this->onAfterDelete($modelEvent);
		if ($modelEvent->isValid) {
			$event = $this->getNodeSocket()->getFrameFactory()->createChannelEventFrame();
			$event
					->setAction('delete.' . get_class($this))
					->setData($this->getAttributes())
					->send();
		}
	}

	protected function beforeLoad(array $attributes) {
		return true;
	}

	protected function afterLoad() {}
}
