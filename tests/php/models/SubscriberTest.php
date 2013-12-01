<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/AModelTest.php';

use YiiNodeSocket\Models\Subscriber;

class SubscriberTest extends AModelTest {

	public function testGetIsNewRecord() {
		$subscriber = new Subscriber();

		$this->assertEquals(true, $subscriber->getIsNewRecord());

		$subscriber->user_id = 1;

		$this->assertEquals(true, $subscriber->save());
		$this->assertEquals(false, $subscriber->getIsNewRecord());

		$_subscriber = Subscriber::model()->findByPk($subscriber->id);
		$this->assertInstanceOf('YiiNodeSocket\Models\Subscriber', $_subscriber);
		$this->assertFalse($_subscriber->getIsNewRecord());

		$this->assertEquals(true, $subscriber->delete(), 'Can not delete subscriber');
	}

	/**
	 * @param Subscriber $subscriber
	 *
	 * @return null|\YiiNodeSocket\Models\AModel
	 * @dataProvider dataProviderForSave
	 */
	public function testSave(Subscriber $subscriber) {
		//  save
		$this->assertEquals(true, $subscriber->save());

		//  check id
		$this->assertEquals(true, !empty($subscriber->id));

		//  update
		$this->assertEquals(true, $subscriber->save());

		$subscriber->user_id = null;
		$subscriber->sid = 'sdfouo8u43u8dfuj34';

		$this->assertEquals(true, $subscriber->save());
		$this->assertEquals('sdfouo8u43u8dfuj34', $subscriber->sid);
		$this->assertNull($subscriber->user_id);

		$subscriber->sid = null;
		$subscriber->user_id = 121;

		$this->assertEquals(true, $subscriber->save());
		$this->assertEquals(121, $subscriber->user_id);
		$this->assertNull($subscriber->sid);

		//  find subscriber by id
		$subscriber = Subscriber::model()->findByPk($subscriber->id);
		$this->assertInstanceOf('YiiNodeSocket\Models\Subscriber', $subscriber);

		$this->assertEquals(true, $subscriber->delete());

		return $subscriber;
	}

	public function testDelete() {
		$subscriber = new Subscriber();
		$subscriber->sid = 'sd8uo329psd';

		$this->assertEquals(true, $subscriber->save());

		$subscriber = Subscriber::model()->findByPk($subscriber->id);

		$this->assertInstanceOf('YiiNodeSocket\Models\Subscriber', $subscriber);

		$this->assertEquals(true, $subscriber->delete());

		$this->assertNull(Subscriber::model()->findByPk($subscriber->id), 'Subscriber need to be null because it has been deleted');
	}

	public function dataProviderForSave() {
		$subscriber1 = new Subscriber();
		$subscriber1->user_id = 1;

		$subscriber2 = new Subscriber();
		$subscriber2->user_id = 1;
		$subscriber2->role = 'user';
		$subscriber2->sid = 'fg9d8uo328uosdfgdf';

		$subscriber3 = new Subscriber();
		$subscriber3->role = 'moderator';
		$subscriber3->sid = '9d8fu09ou439od';

		$subscriber4 = new Subscriber();
		$subscriber4->sid = 'fd98go4u3o94o3';
		$subscriber4->sid_expiration = time() + 300;
		return array(
			array($subscriber1),
			array($subscriber2),
			array($subscriber3),
			array($subscriber4)
		);
	}
}