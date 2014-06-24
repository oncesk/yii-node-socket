<?php

abstract class BaseDriverTest extends CTestCase {

	abstract public function testSave();
	abstract public function testRefresh();
	abstract public function testDelete();
	abstract public function testFindByAttributes();
	abstract public function testFindAllByAttributes();
}