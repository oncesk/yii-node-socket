<?php
namespace YiiSocketTransport\Frame;

require_once 'AFrame.php';
require_once 'Event.php';
require_once 'ChannelEvent.php';
require_once 'VolatileRoomEvent.php';
require_once 'Multiple.php';
require_once 'PublicData.php';

class FrameFactory implements IFrameFactory {

	/**
	 * @var \NodeSocket
	 */
	protected $_nodeSocket;

	/**
	 * @param \NodeSocket $transport
	 */
	public function __construct(\NodeSocket $transport) {
		$this->_nodeSocket = $transport;
	}

	/**
	 * @return Event
	 */
	public function createEventFrame() {
		return new Event($this->_nodeSocket);
	}

	/**
	 * @return ChannelEvent
	 */
	public function createChannelEventFrame() {
		return new ChannelEvent($this->_nodeSocket);
	}

	/**
	 * @return Multiple
	 */
	public function createMultipleFrame() {
		return new Multiple($this->_nodeSocket);
	}

	/**
	 * @return PublicData
	 */
	public function createPublicDataFrame() {
		return new PublicData($this->_nodeSocket);
	}

	/**
	 * @return VolatileRoomEvent
	 */
	public function createVolatileRoomEventFrame() {
		return new VolatileRoomEvent($this->_nodeSocket);
	}


}