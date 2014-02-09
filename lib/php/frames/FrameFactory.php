<?php
namespace YiiNodeSocket\Frames;

require_once 'AFrame.php';
require_once 'Event.php';
require_once 'Authentication.php';
require_once 'UserEvent.php';
require_once 'ChannelEvent.php';
require_once 'Multiple.php';
require_once 'PublicData.php';
require_once 'Invoke.php';
require_once 'JQuery.php';

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
	 * @return Invoke
	 */
	public function createInvokeFrame() {
		return new Invoke($this->_nodeSocket);
	}

	/**
	 * @return JQuery
	 */
	public function createJQueryFrame() {
		return new JQuery($this->_nodeSocket);
	}

	/**
	 * @return Authentication
	 */
	public function createAuthenticationFrame() {
		return new Authentication($this->_nodeSocket);
	}

	/**
	 * @return UserEvent
	 */
	public function createUserEventFrame() {
		return new UserEvent($this->_nodeSocket);
	}
}