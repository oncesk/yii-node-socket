<?php
namespace YiiSocketTransport\Frame;

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
}