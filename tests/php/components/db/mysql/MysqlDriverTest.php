<?php
require_once __DIR__ . '/../../../bootstrap.php';

use YiiNodeSocket\Models\Subscriber;
use YiiNodeSocket\Models\Channel;
use YiiNodeSocket\Models\SubscriberChannel;

class MysqlDriverTest extends BaseDriverTest {

	public function testSave() {
		//  test save subscriber
		$subscriber = new Subscriber();
		$subscriber->user_id = 1;
		$this->assertEquals(true, $subscriber->save(), 'Can not save subscriber');

		$channel = new Channel();
		$channel->name = 'test';
		$this->assertEquals(true, $channel->save(), 'Can not save channel');
	}

	public function testDelete() {
		// TODO: Implement testDelete() method.
	}

	public function testRefresh() {
		// TODO: Implement testRefresh() method.
	}

	public function testFindByAttributes() {
		// TODO: Implement testFindByAttributes() method.
	}

	public function testFindAllByAttributes() {
		// TODO: Implement testFindAllByAttributes() method.
	}

	protected function tearDown() {

	}
}