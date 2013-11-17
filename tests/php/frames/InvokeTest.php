<?php
require_once 'AFrameTest.php';

class InvokeTest extends AFrameTest {

	protected function getFrameClass() {
		return 'Invoke';
	}

	public function testFrameType() {
		//  test frame type
		$this->assertEquals(\YiiNodeSocket\Frame\AFrame::TYPE_INVOKE, Yii::app()->nodeSocket->createInvokeFrame()->getType());
	}

	public function testGetFunctions() {
		$frame = $this->createFrame();
		$this->assertEquals(array(), $frame->getFunctions(), 'Functions should be empty after construct');
	}

	public function getGetMethods() {
		$frame = $this->createFrame();
		$this->assertEquals(array(), $frame->getMethods(), 'Methods should be empty after construct');
	}
}
