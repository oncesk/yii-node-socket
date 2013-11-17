<?php
require_once 'AFrameTest.php';

class EventTest extends AFrameTest {

	/**
	 * @var \YiiNodeSocket\Frame\Event
	 */
	protected $_frame;

	protected function getFrameClass() {
		return 'Event';
	}

	public function testFrameType() {
		//  test frame type
		$this->assertEquals(\YiiNodeSocket\Frame\AFrame::TYPE_EVENT, Yii::app()->nodeSocket->createEventFrame()->getType());
	}

	public function testIsValid() {
		$frame = $this->createFrame();
		$this->assertEquals(true, $frame->isValid());   // true because default event is undefined

		$frame->setEventName('test');
		$this->assertEquals(true, $frame->isValid());
	}

	/**
	 * Test default event name (undefined)
	 */
	public function testDefaultEventName() {
		$metaData = $this->createFrame()->getMetaData();
		$this->assertArrayHasKey('event', $metaData, 'Has no default event name: undefined');
		$this->assertEquals('undefined', $metaData['event']);
	}

	/**
	 * Test setting up event name
	 */
	public function testSetEventName() {
		$frame = $this->createFrame();
		$frame->setEventName('test');

		$meta = $frame->getMetaData();

		$this->assertArrayHasKey('event', $meta, 'Has no event into metadata');
		$this->assertEquals('test', $meta['event']);
	}

	/**
	 * @param $name
	 *
	 * @dataProvider invalidEventNamesDataProvider
	 */
	public function testSetInvalidName($name) {
		$frame = $this->createFrame();
		$meta = $frame->getMetaData();
		$this->assertArrayHasKey('event', $meta, 'Has no event key into metadata');
		$frame->setEventName($name);
		$meta = $frame->getMetaData();
		$this->assertEquals('undefined', $meta['event']);
	}

	public function testSetValidRoomName() {
		$frame = $this->createFrame();
		$frame->setRoom('test');
		$data = $frame->getMetaData();
		$this->assertArrayHasKey('room', $data, 'Frame meta data has no room key');
		$this->assertEquals('test', $data['room']);
	}

	/**
	 * @param $name
	 *
	 * @dataProvider invalidRoomNamesDataProvider
	 */
	public function testSetInvalidRoomName($name) {
		$frame = $this->createFrame();
		$meta = $frame->getMetaData();
		$this->assertArrayNotHasKey('room', $meta, 'Metadata has key room, but do not has it');
		$frame->setRoom($name);
		$meta = $frame->getMetaData();
		$this->assertArrayNotHasKey('room', $meta, 'Meta data has room');
	}

	public function invalidEventNamesDataProvider() {
		return array(
			array(''),
			array(123),
			array(true),
			array(123.12),
			array(array()),
			array(array(1)),
			array(new stdClass())
		);
	}

	public function invalidRoomNamesDataProvider() {
		return array(
			array(''),
			array(true),
			array(array()),
			array(array(1)),
			array(new stdClass())
		);
	}
}