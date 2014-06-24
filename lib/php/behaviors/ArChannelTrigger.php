<?php
namespace YiiNodeSocket\Behaviors;

use YiiNodeSocket\Models\Channel;

/**
 * Class ArChannelTrigger
 * @package YiiNodeSocket\Behaviors
 */
class ArChannelTrigger extends ArBehavior {

	public $triggerOnCreate = true;
	public $triggerOnUpdate = false;
	public $triggerOnDelete = false;

	/**
	 * @var bool
	 */
	public $compareAttributesOnUpdate = true;

	/**
	 * @var array list of attributes to be sent to clients
	 */
	public $attributes = array();

	/**
	 * @var array
	 */
	public $relations = array();

	/**
	 * @var array list of attributes and their values after find or afterSave is it new record
	 */
	protected $_attributes = array();

	public function getRelationEvent($relation) {
		if ($this->getOwner()->hasRelated($relation)) {
			$relation = $this->getOwner()->getActiveRelation($relation);

			if ($relation instanceof \CBelongsToRelation) {
				if (is_string($relation->foreignKey) && $relatedAttributeValue = $this->getOwner()->getAttribute($relation->foreignKey)) {
					try {
						$model = \CActiveRecord::model($relation->className);
						$channel = $model->getChannel();
						if ($channel instanceof Channel) {
							$event = $channel->createEvent();
							$event->setData($this->fetchModelAttributes());
							return $event;
						}
					} catch (\CException $e) {}
				}
			}
		}
		return null;
	}

	protected function triggerRelations($eventPrefix) {
		$multiple = $this->getNodeSocketComponent()->getFrameFactory()->createMultipleFrame();
		foreach ($this->relations as $relation) {
			$eventFrame = $this->getRelationEvent($relation);
			if ($eventFrame) {
				$eventFrame->setEventName($this->createEventName($eventPrefix));
				$multiple->addFrame($eventFrame);
			}
		}
		$multiple->send();
	}

	/**
	 * @param \CModelEvent $event
	 */
	protected function afterSave(\CModelEvent $event) {
		if ($this->getOwner()->getIsNewRecord()) {
			if ($this->triggerOnCreate) {
				$this->triggerRelations('onCreate');
			}
		} else {
			if ($this->triggerOnUpdate) {
				if (!$this->compareAttributesOnUpdate || $this->isAttributesChanged()) {
					$this->triggerRelations('onUpdate');
				}
			}
		}
	}

	/**
	 * @param \CModelEvent $event
	 */
	protected function afterDelete(\CModelEvent $event) {
		if ($this->triggerOnDelete) {
			$this->triggerRelations('onDelete');
		}
	}

	/**
	 * @return bool
	 */
	protected function isAttributesChanged() {
		$diff = array_diff($this->fetchModelAttributes(), $this->_attributes);
		return !empty($diff);
	}

	/**
	 * @param string $prefix
	 *
	 * @return string
	 */
	protected function createEventName($prefix) {
		return $prefix . '.' . strtolower(get_class($this->getOwner()));
	}

	protected function afterFind(\CModelEvent $event) {
		$this->_attributes = $this->fetchModelAttributes();
	}

	/**
	 * @return array
	 */
	protected function fetchModelAttributes() {
		return $this->getOwner()->getAttributes($this->attributes);
	}
}