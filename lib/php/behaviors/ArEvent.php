<?php
namespace YiiNodeSocket\Behaviors;

/**
 * Class ArEvent
 * @package YiiNodeSocket\Behaviors
 */
class ArEvent extends ArBehavior {

	/**
	 * @param string $name
	 *
	 * @return \YiiNodeSocket\Frames\Event
	 */
	public function createEvent($name) {
		$event = $this->getNodeSocketComponent()->getFrameFactory()->createEventFrame();
		$event->setEventName($name);
		return $event;
	}
}