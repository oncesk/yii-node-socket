Yii Node Socket
=================

Connect php, javascript, nodejs in one Yii application.

#Installation

Install nodejs, if not installed see http://nodejs.org/<br>
Install extension

 * Using git clone

```bash
$> git clone git@github.com:oncesk/yii-node-socket.git
```
 * As a submodule<br>

> ext_directory - extension folder into your yii application (like protected/extensions/node-socket)

```bash
$> git submodule add git@github.com:oncesk/yii-node-socket.git ext_directory
$> git submodule update
```
Now go to the folder where you install extension  ***application.ext.yii-node-socket*** and execute<br>
```bash
$> git submodule init
$> git submodule update
```

Yii configuration<br>
 * Configure console command in (***main/console.php***). You can use config below:

```php
'commandMap' => array(
	'node-socket' => 'application.ext.yii-node-socket.lib.php.NodeSocketCommand'
)
```

 * Register Yii component, need to add into **main.php and console.php**:

```php
'socketTransport' => array(
	'class' => 'ext.yii-node-socket.lib.php.NodeSocket',
	'host' => '127.0.0.1',	// default is 127.0.0.1, can be ip or domain name, without http
	'port' => 3001		// default is 3001, should be integer
)
```

Install ***nodejs*** components in ***application.ext.yii-node-socket.lib.js.server***:
```bash
$> npm install
```

Congratulation, installation completed!

> Notice: if the name of the component will not be **nodeSocket**, your need to use special key in console command --componentName=component_name

###Console command actions

Use (**./yiic node-socket**)

```bash
$> ./yiic node-socket # show help
$> ./yiic node-socket start # start server
$> ./yiic node-socket stop # stop server
$> ./yiic node-socket restart # restart server
$> ./yiic node-socket getPid # show pid of nodejs process
```

##Javascript

Before use in javascript, register client stripts like here

```php
public function actionIndex() {
	// register node socket scripts
	Yii::app()->nodeSocket->registerClientScripts();
}
```

###Events

Predefined events:

* `listener.on('connect', function () {})` - "connect" is emitted when the socket connected successfully
* `listener.on('reconnect', function () {})` - "reconnect" is emitted when socket.io successfully reconnected to the server
* `listener.on('disconnect', function () {})` - "disconnect" is emitted when the socket disconnected

Your own events:

* `listener.on('update', function (data) {})` - emitted when PHP server emit update event
* `listener.on('some_event', function (data) {})` - emitted when PHP server emit some_event event

###Work in javascript

Use `YiiNodeSocket` class

####Start work

```javascript

// create object
var socket = new YiiNodeSocket();

// enable debug mode
socket.debug(true);
```

####Catch Events

Now events can be created only on PHP side. All data transmitted in json format.
Into callback function data was pasted as javascript native object (or string, integer, depends of  your PHP Frame config)

```javascript
// add event listener
socket.on('updateBoard', function (data) {
	// do any action
});
```

####Rooms

```javascript
socket.room('testRoom').join(function (success, numberOfRoomSubscribers) {
	// success - boolean, numberOfRoomSubscribers - number of room members
	// if error occurred then success = false, and numberOfRoomSubscribers - contains error message
	if (success) {
		console.log(numberOfRoomSubscribers + ' clients in room: ' + roomId);
		// do something
		
		// bind events
		this.on('join', function (newMembersCount) {
			// fire on client join
		});
		
		this.on('data', function (data) {
			// fire when server send frame into this room with 'data' event
		});
	} else {
		// numberOfRoomSubscribers - error message
		alert(numberOfRoomSubscribers);
	}
});
```

####Shared Public Data

You can set shared data only from PHP using PublicData Frame (see below into PHP section).
To access data you can use `getPublicData(string key, callback fn)` method

```javascript
socket.getPublicData('error.strings', function (strings) {
	// you need to check if strings exists, because strings can be not setted or expired,
	if (strings) {
		// do something
	}
});
```


##PHP

####Client scripts registration

```php
public function actionIndex() {
	...

	Yii::app()->nodeSocket->registerClientScripts();
	
	...
}
```

####Event frame

```php

...

// create event frame
$frame = Yii::app()->nodeSocket->createEventFrame();

// set event name
$frame->setEventName('updateBoard');

// set data using ArrayAccess interface
$frame['boardId'] = 25;
$frame['boardData'] = $html;

// or you can use setData(array $data) method
// setData overwrite data setted before

$frame->send();

...

```

####Set up shared data

You can set expiration using ***setLifeTime(integer $lifetime)*** method of class PublicData

```php

...

// create frame
$frame = Yii::app()->nodeSocket->createPublicDataFrame();

// set key in storage
$frame->setKey('error.strings');

// set data
$frame->setData($errorStrings);

// you can set data via ArrayAccess interface
// $frame['empty_name'] = 'Please enter name';

// set data lifetime
$frame->setLifeTime(3600*2);	// after two hours data will be deleted from storage

// send
$frame->send();

...

```

####Room events

```php

...

// create frame
$frame = Yii::app()->nodeSocket->createEventFrame();

// set event name
$frame->setEventName('updateBoard');

// set room name
$frame->setRoom('testRoom');

// set data
$frame['key'] = $value;

// send
$frame->send();

...

```

Событие смогут отловить клиенты, которые состоят в комнате testRoom

####Отправка нескольких событий за один раз

Для отправки нескольких событий используйте 

```php

$multipleFrame = Yii::app()->nodeSocket->createMultipleFrame();

$eventFrame = Yii::app()->nodeSocket->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$dataEvent = Yii::app()->nodeSocket->createPublicDataFrame();

$dataEvent->setKey('error.strings');
$dataEvent['key'] = $value;

$multipleFrame->addFrame($eventFrame);
$multipleFrame->addFrame($dataEvent);
$multipleFrame->send();

```

Можно и так

```php

$multipleFrame = Yii::app()->nodeSocket->createMultipleFrame();

$eventFrame = $multipleFrame->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$dataEvent = $multipleFrame->createPublicDataFrame();

$dataEvent->setKey('error.strings');
$dataEvent['key'] = $value;

$multipleFrame->send();

```

##В планах

1. Создать систему subscribe/unsibscribe, с созданием, удалением, сохранением каналов, добавления и удаления подписчиков
2. Хранение информации и канале и подписчиках в базе данных, скорее всего mongoDB
3. Возможность создавать каналы с разными правами (публичный канал, публичный ограниченный, приватный, приватный ограниченный)
4. Аутентификация сокета
