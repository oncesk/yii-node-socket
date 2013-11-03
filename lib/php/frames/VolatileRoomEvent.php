<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 11/3/13
 * Time: 2:58 PM
 * To change this template use File | Settings | File Templates.
 */

namespace YiiSocketTransport\Frame;

/**
 * Class VolatileRoomEvent
 *
 * @package YiiSocketTransport\Frame
 */
class VolatileRoomEvent extends Event {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_VOLATILE_ROOM_EVENT;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return (parent::isValid() && $this->hasMetaData('roomId'));
	}

	/**
	 * @param $id
	 *
	 * @return VolatileRoomEvent
	 */
	public function setRoomId($id) {
		if (is_string($id) || is_numeric($id)) {
			$this->addMetaData('roomId', $id);
		}
		return $this;
	}
}