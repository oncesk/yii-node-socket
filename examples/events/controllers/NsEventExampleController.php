<?php
class NsEventExampleController extends Controller {

	public function actionIndex() {
		$this->render('index');
	}

	public function actionSendEvent() {
		$event = Yii::app()->nodeSocket->createEventFrame();
		$event->setEventName('event.example');
		$event['data'] = array(
			1,
			array(
				'red',
				'black',
				'white'
			),
			new stdClass(),
			'simple string'
		);
		$event->send();
		$this->render('sendEvent');
	}

	public function actionSendRoomEvent() {
		$this->render('sendRoomEvent');
	}

	public function actionEventListener() {
		Yii::app()->nodeSocket->registerClientScripts();
		$this->render('eventListener');
	}
}