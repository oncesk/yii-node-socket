<?php
namespace YiiNodeSocket\Frames;

class UserEvent extends Event {

	/**
	 * @return string
	 */
	public function getType() {
		return 'userEvent';
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return parent::isValid() && $this->hasMetaData('uid');
	}

	/**
	 * @param integer|array $id
	 *
	 * @return UserEvent
	 */
	public function setUserId($id) {
		if (!is_array($id)) {
			$id = (array) $id;
		}
		$this->addMetaData('uid', $id);
		return $this;
	}
}