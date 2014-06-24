<?php
class AModelTest extends CTestCase {

	/**
	 * @param \YiiNodeSocket\Models\AModel $model
	 *
	 * @dataProvider modelsDataProvider
	 */
	public function testGetNodeSocket(\YiiNodeSocket\Models\AModel $model) {
		$this->assertInstanceOf('NodeSocket', $model->getNodeSocket());
	}

	/**
	 * @param \YiiNodeSocket\Models\AModel $model
	 *
	 * @dataProvider modelsDataProvider
	 */
	public function testGetDbDriver(\YiiNodeSocket\Models\AModel $model) {
		$this->assertInstanceOf('YiiNodeSocket\Components\Db\DriverInterface', $model->getDbDriver());
	}

	public function modelsDataProvider() {
		return array(
			array(new \YiiNodeSocket\Models\Subscriber()),
			array(new \YiiNodeSocket\Models\Channel()),
			array(new \YiiNodeSocket\Models\SubscriberChannel())
		);
	}
}