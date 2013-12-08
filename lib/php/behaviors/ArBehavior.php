<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Components\ArEvent;

/**
 * Class ArBehavior
 *
 * @method \CActiveRecord getOwner()
 *
 * @package YiiNodeSocket\Behavior
 */
abstract class ArBehavior extends \CActiveRecordBehavior {

	/**
	 * @var string
	 */
	public $componentName = 'nodeSocket';

	const ERROR_CAN_NOT_CREATE = 1;
	const ERROR_CAN_NOT_UPDATE = 2;
	const ERROR_CAN_NOT_DELETE = 3;

	/**
	 * @return \NodeSocket
	 * @throws \CException
	 */
	public function getNodeSocketComponent() {
		if (!\Yii::app()->hasComponent($this->componentName)) {
			throw new \CException('Node socket component not found with the name `' . $this->componentName . "`");
		}
		return \Yii::app()->getComponent($this->componentName);
	}

	/**
	 * @param ArEvent $event
	 */
	protected function triggerModelEvent(ArEvent $event) {
		$owner = $this->getOwner();
		if ($owner->hasEvent($event->name)) {
			$owner->raiseEvent($event->name, $event);
		}
	}
}