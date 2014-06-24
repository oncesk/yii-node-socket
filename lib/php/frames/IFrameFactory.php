<?php
namespace YiiNodeSocket\Frames;

interface IFrameFactory {

	/**
	 * @return Event
	 */
	public function createEventFrame();

	/**
	 * @return ChannelEvent
	 */
	public function createChannelEventFrame();

	/**
	 * @return Multiple
	 */
	public function createMultipleFrame();

	/**
	 * @return PublicData
	 */
	public function createPublicDataFrame();

	/**
	 * @return Invoke
	 */
	public function createInvokeFrame();

	/**
	 * @return JQuery
	 */
	public function createJQueryFrame();

	/**
	 * @return UserEvent
	 */
	public function createUserEventFrame();

	/**
	 * @return Authentication
	 */
	public function createAuthenticationFrame();
}