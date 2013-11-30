<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/AModelTest.php';

use YiiNodeSocket\Models\Channel;

class ChannelTest extends AModelTest {

	public function testSaveWithInvalidChannel(Channel $channel) {

	}

	/**
	 * @param Channel $channel
	 *
	 * @dataProvider validaDataProviderForTestSave
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
			'name' => 'test6',
			'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
			'event_source' => Channel::EVENT_SOURCE_PHP
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

	public function validaDataProviderForTestSave() {
		$provider = array();
		foreach ($this->getValidChannelDataArray() as $attributes) {
			$channel = new Channel();
			$channel->attributes = $attributes;
			$provider[] = array($channel);
		}
		return $provider;
	}

	protected function getValidChannelDataArray() {
		return array(
			array(
				'name' => 'test',
			),
			array(
				'name' => 'test1',
				'is_authentication_required' => true,
			),
			array(
				'name' => 'test2',
				'is_authentication_required' => false,
			),
			array(
				'name' => 'test3',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_JAVASCRIPT
			),
			array(
				'name' => 'test4',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP
			),
			array(
				'name' => 'test5',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT
			),
			array(
				'name' => 'test6',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_PHP
			),
			array(
				'name' => 'test7',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_PHP_OR_JAVASCRIPT
			),
			array(
				'name' => 'test8',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_JAVASCRIPT
			),
			array(
				'name' => 'test9',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_JAVASCRIPT,
				'is_authentication_required' => true,
				'allowed_roles' => 'user, moderator'
			),
			array(
				'name' => 'test10',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_JAVASCRIPT,
				'is_authentication_required' => true,
				'allowed_roles' => array(
					'user',
					'moderator'
				)
			),
			array(
				'name' => 'test11',
				'subscriber_source' => Channel::SUBSCRIBER_SOURCE_PHP_OR_JAVASCRIPT,
				'event_source' => Channel::EVENT_SOURCE_JAVASCRIPT,
				'is_authentication_required' => false,
				'allowed_roles' => array(
					'user',
					'moderator'
				)
			)
		);
	}
}