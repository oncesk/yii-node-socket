<?php
namespace YiiNodeSocket\Frames;

/**
 * Class Multiple
 *
 * Needed for send many frames at a time
 *
 * Usage
 *
 * $eventFrame = new Event();
 * $eventFrame->setEventName('user.join');
 * $eventFrame['userId'] = 12;
 *
 * $subscriptionFrame = new Subscription();
 * $subscriptionFrame->subscribe('users.channel');
 *
 * $multiple = new Multiple()
 * $multiple
 *      ->addFrame($eventFrame)
 *      ->addFrame($subscriptionFrame)
 *      ->send();
 *
 * @package YiiNodeSocket\Frame
 */
class Multiple extends AFrame implements IFrameFactory {

	/**
	 * @var array
	 */
	protected $_frames = array();

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_MULTIPLE_FRAME;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return !empty($this->_frames);
	}

	/**
	 * @return ChannelEvent
	 */
	public function createChannelEventFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createChannelEventFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return Event
	 */
	public function createEventFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createEventFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return Multiple
	 */
	public function createMultipleFrame() {
		return $this;
	}

	/**
	 * @return PublicData
	 */
	public function createPublicDataFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createPublicDataFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return Invoke
	 */
	public function createInvokeFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createInvokeFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return JQuery
	 */
	public function createJQueryFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createJQueryFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return Authentication
	 */
	public function createAuthenticationFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createAuthenticationFrame()
				->setAsMultiple($this);
	}

	/**
	 * @return UserEvent
	 */
	public function createUserEventFrame() {
		return $this
				->_nodeSocket->getFrameFactory()
				->createUserEventFrame()
				->setAsMultiple($this);
	}


	/**
	 * @param AFrame $frame
	 *
	 * @return Multiple
	 */
	public function addFrame(AFrame $frame) {
		if (!array_key_exists($frame->getType(), $this->_frames)) {
			$this->_frames[$frame->getType()] = array();
		}
		if (!array_key_exists($frame->getId(), $this->_frames[$frame->getType()])) {
			$this->_frames[$frame->getType()][$frame->getId()] = $frame;
		}
		return $this;
	}

	protected function beforeSend() {
		$data = array();
		$metaData = array();
		/** @var AFrame $frame */
		foreach ($this->_frames as $type => $f) {
			$data[$type] = array();
			$metaData[$type] = array();
			foreach ($f as $id => $frame) {

				//  check if frame is valid frame
				if ($frame->isValid()) {

					//  prepare frame
					$frame->prepareFrame();

					//  collect frame data
					$data[$type][$id] = $frame->getData();
					$metaData[$type][$id] = $frame->getMeta();
				}
			}
			if (empty($data[$type]) && empty($metaData[$type])) {
				unset(
					$data[$type],
					$metaData[$type]
				);
			}
		}
		if (empty($data) && empty($metaData)) {
			return false;
		}
		$this->setData($data);
		$this->setMetaData($metaData);
		return true;
	}
}