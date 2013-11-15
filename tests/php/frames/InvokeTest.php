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
}
