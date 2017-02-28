<?php
namespace YiiNodeSocket\Frames;

require_once __DIR__.'/AFrame.php';
require_once __DIR__.'/Event.php';
require_once __DIR__.'/Authentication.php';
require_once __DIR__.'/UserEvent.php';
require_once __DIR__.'/ChannelEvent.php';
require_once __DIR__.'/Multiple.php';
require_once __DIR__.'/PublicData.php';
require_once __DIR__.'/Invoke.php';
require_once __DIR__.'/JQuery.php';

class FrameFactory implements IFrameFactory {

	/**
	 * @var \NodeSocket
	 */
	protected $_nodeSocket;

	/**
	 * @param \NodeSocket $transport
	 */
	public function __construct(\YiiNodeSocket\NodeSocket $transport) {
		$this->_nodeSocket = $transport;
	}

	/**
	 * @return Event
	 */
	public function createEventFrame() {
		$evt = new Event($this->_nodeSocket);
                return $evt;
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