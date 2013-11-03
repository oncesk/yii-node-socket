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
	 * @var \SocketTransport
	 */
	protected $_socketTransport;

	/**
	 * @param \SocketTransport $transport
	 */
	public function __construct(\SocketTransport $transport) {
		$this->_socketTransport = $transport;
	}

	/**
	 * @return Event
	 */
	public function createEventFrame() {
		return new Event($this->_socketTransport);
	}

	/**
	 * @return ChannelEvent
	 */
	public function createChannelEventFrame() {
		return new ChannelEvent($this->_socketTransport);
	}

	/**
	 * @return Multiple
	 */
	public function createMultipleFrame() {
		return new Multiple($this->_socketTransport);
	}

	/**
	 * @return PublicData
	 */
	public function createPublicDataFrame() {
		return new PublicData($this->_socketTransport);
	}

	/**
	 * @return VolatileRoomEvent
	 */
	public function createVolatileRoomEventFrame() {
		return new VolatileRoomEvent($this->_socketTransport);
	}


}