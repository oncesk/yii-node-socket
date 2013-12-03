<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/AModelTest.php';

use YiiNodeSocket\Models\Channel;
use YiiNodeSocket\Models\Subscriber;

class ChannelTest extends AModelTest {

	public function testSubscribe() {
		$subscriber = new Subscriber();
		$subscriber->sid = microtime();
		$this->assertEquals(true, $subscriber->save(), 'Can not save subscriber');

		$channel = new Channel();
		$channel->name = microtime();
		$this->assertEquals(true, $channel->save());

		$this->assertEmpty($channel->getSubscribers());

		$this->assertEquals(true, $channel->subscribe($subscriber));

		$subscribers = $channel->getSubscribers();
		$this->assertNotEmpty($subscribers);
		$this->assertCount(1, $subscribers);
		$this->assertEquals(true, $subscriber->id == $subscribers[$subscriber->id]->id);

		$secondSubscriber = new Subscriber();
		$secondSubscriber->sid = 'sdf0s9duf32' . time();
		$this->assertEquals(true, $secondSubscriber->save());

		$this->assertEquals(true, $channel->subscribe($secondSubscriber));
		$subscribers = $channel->getSubscribers();
		$this->assertCount(2, $subscribers);
		$this->assertEquals(true, $secondSubscriber->id == $subscribers[$secondSubscriber->id]->id);

		//  load channel
		$channel = Channel::model()->findByPk($channel->id);
		$this->assertInstanceOf('YiiNodeSocket\Models\Channel', $channel);
		/** @var Channel $channel */
		$subscribers = $channel->getSubscribers();
		$this->assertNotEmpty($subscribers);
		$this->assertCount(2, $subscribers);

		return $channel;
	}

	/**
	 * @param Channel $channel
	 *
	 * @depends testSubscribe
	 */
	public function testUnSubscriber(Channel $channel) {
		$subscribers = $channel->getSubscribers();
		$this->assertCount(2, $subscribers);
		$subscriber = array_pop($subscribers);

		$this->assertTrue($channel->unSubscribe($subscriber));
		$subscribers = $channel->getSubscribers();
		$this->assertCount(1, $subscribers);
		$subscriber = array_pop($subscribers);

		$this->assertTrue($channel->unSubscribe($subscriber));
		$subscribers = $channel->getSubscribers();
		$this->assertCount(0, $subscribers);

		$this->assertCount(0, $channel->getSubscribers());
		$this->assertTrue($channel->delete());
	}

	/**
	 * @dataProvider invalidDataProviderForTestSaveWithInvalidChannel
	 */
	public function testSaveWithInvalidChannel(Channel $channel) {
		//  test is new record
		$this->assertEquals(true, $channel->getIsNewRecord());

		//  test save
		$this->assertEquals(false, $channel->save());

		//  has errors
		$this->assertEquals(true, $channel->hasErrors());

		//  test new record
		$this->assertEquals(true, $channel->getIsNewRecord());
	}

	/**
	 * @param Channel $channel
	 *
	 * @dataProvider validDataProviderForTestSave
	 */
	public function testSave(Channel $channel) {

		//  test is new record
		$this->assertEquals(true, $channel->getIsNewRecord());

		//  test save
		$this->assertEquals(true, $channel->save());

		//  test new record
		$this->assertEquals(false, $channel->getIsNewRecord());

		//  repeat save
		$this->assertEquals(true, $channel->save());

		//  change name
		$name = $channel->name .= 'changed';
		$this->assertEquals(true, $channel->save());
		$this->assertEquals($name, $channel->name);

		//  load by pk
		$_channel = Channel::model()->findByPk($channel->id);
		$this->assertInstanceOf('YiiNodeSocket\Models\Channel', $_channel);

		//  test get is new record
		$this->assertEquals(false, $_channel->getIsNewRecord());

		//  test delete
		$this->assertEquals(true, $channel->delete());
	}

	public function testDelete() {
		$channel = new Channel();
		$channel->attributes = array(
			'name' => 'test6' . time(),
			'subscriber_source' => Channel::SOURCE_PHP,
			'event_source' => Channel::SOURCE_PHP
		);

		$this->assertEquals(true, $channel->getIsNewRecord());
		$this->assertEquals(true, $channel->save());

		//  try find channel
		$channel = Channel::model()->findByPk($channel->id);
		$this->assertInstanceOf('YiiNodeSocket\Models\Channel', $channel);
		//  try delete
		$this->assertEquals(true, $channel->delete());

		//  try find again
		$channel = Channel::model()->findByPk($channel->id);
		//  should be null
		$this->assertNull($channel);
	}

	public function invalidDataProviderForTestSaveWithInvalidChannel() {
		$provider = array();
		foreach ($this->getInValidChannelDataArray() as $attributes) {
			$channel = new Channel();
			$channel->attributes = $attributes;
			$provider[] = array($channel);
		}
		return $provider;
	}

	public function validDataProviderForTestSave() {
		$provider = array();
		foreach ($this->getValidChannelDataArray() as $attributes) {
			$attributes['name'] .= time();
			$channel = new Channel();
			$channel->attributes = $attributes;
			$provider[] = array($channel);
		}
		return $provider;
	}

	protected function getInValidChannelDataArray() {
		return array(
			array(
				'name' => '',
				'properties' => false
			),
			array(
				'name' => array(),
				'properties' => 12.2
			),
			array(
				'name' => false,
				'is_authentication_required' => '1dssds',
				'properties' => function () {

				}
			),
			array(
				'name' => 'test3',
				'subscriber_source' => 121
			),
			array(
				'name' => 'test6',
				'subscriber_source' => array(),
				'event_source' => 'sdfgsdf'
			),
			array(
				'is_authentication_required' => 2342,
				'allowed_roles' => 23423
			)
		);
	}

	protected function getValidChannelDataArray() {
		return array(
			array(
				'name' => 'test',
				'properties' => '{}'
			),
			array(
				'name' => 'test1',
				'is_authentication_required' => true,
				'properties' => array(
					'test' => 'value'
				)
			),
			array(
				'name' => 'test2',
				'is_authentication_required' => false,
				'properties' => new stdClass()
			),
			array(
				'name' => 'test3',
				'subscriber_source' => Channel::SOURCE_JAVASCRIPT
			),
			array(
				'name' => 'test4',
				'subscriber_source' => Channel::SOURCE_PHP
			),
			array(
				'name' => 'test5',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT
			),
			array(
				'name' => 'test6',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_PHP
			),
			array(
				'name' => 'test7',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT
			),
			array(
				'name' => 'test8',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_JAVASCRIPT
			),
			array(
				'name' => 'test9',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_JAVASCRIPT,
				'is_authentication_required' => true,
				'allowed_roles' => 'user, moderator'
			),
			array(
				'name' => 'test10',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_JAVASCRIPT,
				'is_authentication_required' => true,
				'allowed_roles' => array(
					'user',
					'moderator'
				)
			),
			array(
				'name' => 'test11',
				'subscriber_source' => Channel::SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::SOURCE_JAVASCRIPT,
				'is_authentication_required' => false,
				'allowed_roles' => array(
					'user',
					'moderator'
				)
			)
		);
	}
}