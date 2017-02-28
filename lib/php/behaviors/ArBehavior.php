<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Components\ArEvent;
use yii\behaviors\AttributeBehavior;
/**
 * Class ArBehavior
 *

 *
 * @package YiiNodeSocket\Behavior
 */
abstract class ArBehavior extends AttributeBehavior {

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
		if (!\Yii::$app->components($this->componentName)) {
			throw new \CException('Node socket component not found with the name `' . $this->componentName . "`");
		}
		return \Yii::$app->getComponent($this->componentName);
	}
        
        public function getOwner(){
            return $this->owner;
        }

	/**
	 * @param ArEvent $event
	 */
	protected function triggerModelEvent(ArEvent $event) {
		$owner = $this->getOwner();
		if ($owner->hasEventHandlers($event->name)) {
			$owner->trigger($event->name, $event);
		}
	}
}
