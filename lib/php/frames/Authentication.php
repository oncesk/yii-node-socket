<?php
namespace YiiNodeSocket\Frames;

class Authentication extends AFrame {

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->hasMetaData('uid');
	}

	/**
	 * @return string
	 */
	public function getType() {
		return 'auth';
	}

	/**
	 * @param integer $id
	 *
	 * @return $this
	 */
	public function setUserId($id) {
		if ($id && is_numeric($id)) {
			$this->addMetaData('uid', $id);
		}
		return $this;
	}
}