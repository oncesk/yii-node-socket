<?php
namespace YiiNodeSocket\Frames;

class PublicData extends AFrame {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_PUBLIC_DATA;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->hasMetaData('key');
	}

	/**
	 * If $lifetime > 0 than data will be destroyed after that limit
	 *
	 * @param integer $lifetime
	 *
	 * @return PublicData
	 */
		public function setLifeTime($lifetime) {
		if (is_int($lifetime) || is_numeric($lifetime)) {
			$this->addMetaData('lifetime', (int) $lifetime);
		}
		return $this;
	}

	/**
	 * @param $key
	 *
	 * @return PublicData
	 */
	public function setKey($key) {
		if (is_string($key)) {
			$this->addMetaData('key', $key);
		}
		return $this;
	}

	protected function init() {
		$this->setLifeTime(0);
	}
}