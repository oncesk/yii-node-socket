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

	const ERROR_CAN_NOT_CREATE = 1;
	const ERROR_CAN_NOT_UPDATE = 2;
	const ERROR_CAN_NOT_DELETE = 3;

	/**
	 * @param ArEvent $event
	 */
	protected function triggerModelEvent(ArEvent $event) {
		$this->getOwner()->raiseEvent($event->name, $event);
	}
}