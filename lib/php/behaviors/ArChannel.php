<?php
namespace YiiNodeSocket\Behavior;

use YiiNodeSocket\Models\Channel;

class ArChannel extends ArBehavior {

	/**
	 * @var string if set in javascript you can catch events for this alias
	 */
	public $alias;

	/**
	 * @var array list of attributes which can be received into javascript
	 */
	public $attributes;

	/**
	 * @var string
	 */
	public $nodeSocketComponent = 'nodeSocket';

	public function attach($owner) {
		parent::attach($owner);
		if (!\Yii::app()->hasComponent($this->nodeSocketComponent)) {
			throw new \CException('Node socket component not found');
		}
	}

	public function afterSave(\CModelEvent $event) {
		$pk = $this->getOwner()->getPrimaryKey();
		if (is_array($pk)) {
			$pk = md5(\CJSON::encode($pk));
		}
		$name = get_class($this->getOwner()) . ':' . $pk;
		$channel = Channel::model()->findByAttributes(array(
			'name' => $name
		));
		if (!$channel) {
			$channel = new Channel();
			$channel->name = $name;
		}
	}
}