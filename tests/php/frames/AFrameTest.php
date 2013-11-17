<?php
require_once __DIR__ . '/../bootstrap.php';

use YiiNodeSocket\Frame\Event;
use YiiNodeSocket\Frame\Multiple;
use YiiNodeSocket\Frame\Invoke;
use YiiNodeSocket\Frame\PublicData;

abstract class AFrameTest extends CTestCase {

	/**
	 * @var NodeSocket
	 */
	protected $_nodeSocket;

	/**
	 * @var \YiiNodeSocket\Frame\AFrame
	 */
	protected $_frame;

	protected function setUp() {
		$this->_nodeSocket = Yii::app()->nodeSocket;
		$this->_frame = $this->createFrame();
	}

	abstract protected function getFrameClass();

	/**
	 * @return \YiiNodeSocket\Frame\AFrame
	 */
	protected function createFrame() {
		return Yii::app()->nodeSocket->{'create' . $this->getFrameClass() . 'Frame'}();
	}

	public function testCreateEventFrameViaNodeSocketComponent() {
		$this->assertInstanceOf('YiiNodeSocket\Frame\\' . $this->getFrameClass() , Yii::app()->nodeSocket->{'create' . $this->getFrameClass() . 'Frame'}());
	}

	public function testCreateEventFrameViaMultipleFrame() {
		$frame = Yii::app()->nodeSocket->createMultipleFrame();
		$this->assertInstanceOf('YiiNodeSocket\Frame\Multiple', $frame);
		$this->assertInstanceOf('YiiNodeSocket\Frame\\' . $this->getFrameClass(), $frame->{'create' . $this->getFrameClass() . 'Frame'}());
	}

	public function testMetaDataType() {
		$this->assertEquals(true, is_array($this->_frame->getMetaData()));
	}
}