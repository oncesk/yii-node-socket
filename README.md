Yii Node Socket
=================

Connect php, javascript, nodejs in one Yii application.

####What you can do:

- send event(s) to all clients or in concrete room or in concrete channel
- now you can send events to single (concrete user) by user id!
- call some function or object method in window context
- you can change DOM model with jquery from php
- ability to set up data and get it in your javascript application
- send events from javascript for all clients or clients in concrete room or channel

##Changes

 - for frames creation you should use Yii::app()->nodeSocket->getFrameFactory()

##Requirements

 - linux/unix/windows
 - git
 - vps or dedicated server (for nodejs process)

#Installation

Install nodejs, if not installed see http://nodejs.org/<br>
Install extension

 * Using git clone

```bash
$> git clone git@github.com:oncesk/yii-node-socket.git
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
	'node-socket' => 'application.extensions.yii-node-socket.lib.php.NodeSocketCommand'
)
```

 * Register Yii component, need to add into **main.php and console.php**:

```php
'nodeSocket' => array(
	'class' => 'application.extensions.yii-node-socket.lib.php.NodeSocket',
	'host' => 'localhost',	// default is 127.0.0.1, can be ip or domain name, without http
	'port' => 3001		// default is 3001, should be integer
)
```
> Notice: ***host*** should be a domain name like in you virtual host configuration or server ip address if you request page using ip address


> Notice: if you will be use ***behaviors*** or node-socket models, you need to add nodeSocket component in ***preload*** components list

```php

'preload' => array(
	'nodeSocket'
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

##Definitions

 - Frame - data package for nodejs server wrapped into Class. Per one request to nodejs server you can send only 1 frame. For send several frames at a time use Multiple frame.
 - room - one or more clients in concrete namespace: every client can create room, other clients can join into concrete room, any client in room can send event in this room.

##Javascript

Before use in javascript, register client stripts like here

```php
public function actionIndex() {
	// register node socket scripts
	Yii::app()->nodeSocket->registerClientScripts();
}
```

###Events

###Work in javascript

Use `YiiNodeSocket` class

####Start work

```javascript

// create object
var socket = new YiiNodeSocket();

// enable debug mode
socket.debug(true);

socket.onConnect(function () {
	// fire when connection established
});

socket.onDisconnect(function () {
	// fire when connection close or lost
});

socket.onConnecting(function () {
	// fire when the socket is attempting to connect with the server
});

socket.onReconnect(function () {
	// fire when successfully reconnected to the server
});
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
socket.onConnect(function () {
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
});

```

####Channels

Channel is very similar to the room, but you can controll access to channel for clients

```javascript

// join to channel, join needed when you try subscribe to channel from javascript, if you subscribed to channel in php you can bind events without join
socket.onConnect(function () {
	var testChannel = socket.channel('test').join(function (success) {
		// success - boolean
		if (success) {
			// fore getting channel attributes
			console.log(this.getAttributes());
			
			// bind event listeners
			this.on('some_event', function (data) {
				// fire when server send frame into this room with 'data' event
			});
		} else {
			console.log(this.getError());
		}
	});
	
	// you can bind events handlers for some events without join
	// in this case you should be subscribed to `test` channel
	socket.channel('test').on('some_event', function (data) {
	});

});
```

####Emit events

You can emit event to:
 - all clients
 - clients in concrete room


Global events:

```javascript

socket.emit('global.event', {
	message : {
		id : 12,
		title : 'This is a test message'
	}
});

socket.on('global.event', function (data) {
	console.log(data.message.title); // you will see in console `This is a test message`
});

```

Room event:

```javascript
socket.onConnect(function () {
	var testRoom = socket.room('testRoom').join(function (success, numberOfRoomSubscribers) {
		// success - boolean, numberOfRoomSubscribers - number of room members
		// if error occurred then success = false, and numberOfRoomSubscribers - contains error message
		if (success) {
			console.log(numberOfRoomSubscribers + ' clients in room: ' + roomId);
			// do something
			
			// bind events
			this.on('message', function (message) {
				console.log(message);
			});
			
			this.on('ping', function () {
				console.log('Ping!');
			});
			
			this.emit('ping'); // emit ping event
		} else {
			// numberOfRoomSubscribers - error message
			alert(numberOfRoomSubscribers);
		}
	});
	
	// emit message event
	testRoom.emit('message', {
		message : {
			id : 12,
			title : 'This is a test message'
		}
	});
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

####Behaviors

 - YiiNodeSocket\Behaviors\ArChannel - can be used for create new channel. Example: You can attach this behavior to User, in result any user will have own channel, and other user can subscribe to events of concrete user.
 - YiiNodeSocket\Behaviors\ArSubscriber - should be attached to object which can subscribe to some channel. Example: model User at a time can be channel and subscriber.

```php

/**
 *
 * @method \YiiNodeSocket\Models\Channel getChannel()
 * @method \YiiNodeSocket\Frames\Event|null createEvent($name)
 */
class User extends CActiveRecord {

...

	public function behaviors() {
		return array(
			// attach channel behavior
			'channel' => array(
				'class' => '\YiiNodeSocket\Behaviors\ArChannel',
				'updateOnSave' => true
			),
			// attach subscriber behavior
			'subscriber' => array(
				'class' => '\YiiNodeSocket\Behaviors\ArSubscriber'
			)
		);
	}
	
...
	
}

// Example of subscribe

$user1 = new User();
$user1->setAttributes($attributes);
if ($user1->save()) {
	// imagine that $user1->id == 122
	// user channel was created and sent to nodejs server
	// subscriber was created and sent to nodejs server
	
	// create second user
	$user2 = User::model()->findByPk(121);
	
	// now we can subscribe one user to second user
	$user1->subscribe($user2);
	// and $user2 can catch events from $user1 channel like in twitter
	
	
}


// Example of emit event in concrete channel

$user = User::model()->findByPk(122);
if ($user) {

	// First method
	$event = $user->createEvent('test_event');
	if ($event) {
		// set event data
		$event->setData(array(
			'black', 'red', 'white'
		));
		// send event to user channel
		$event->send();
	}
	
	// Second method with getting channel
	$channel = $user->getChannel();
	if ($channel) {
		$event = $channel->createEvent('test_event');
		// set event data
		$event->setData(array(
			'black', 'red', 'white'
		));
		// send event to user channel
		$event->send();
	}
}


// Example of unsubscribe

$user1 = User::model()->findByPk(122);
$user2 = User::model()->findByPk(121);

$user1->unSubscribe($user2); // now $user2 can not catch events in channel of $user1

```
 
####Client authorization

For authorizing client you need send special Authentication frame, you can do it in your ***user*** component in afterLogin event

```php

protected function afterLogin($fromCookie) {
	parent::afterLogin($fromCookie);
	
	$frame = Yii::app()->nodeSocket->getFrameFactory()->createAuthenticationFrame();
	$frame->setUserId($this->getId());
	$frame->send();
}


```

After that, this user can receive events only for him.
See example below, send event only for single (concrete) $user

```php

// $user - the user model, which can receive this event

$event = Yii::app()->nodeSocket->getFrameFactory()->createUserEventFrame();
$event->setUserId($user->id);
$event->setEventName('message');
$event['text'] = 'Hello, how are you?';
$event->send();

```

and your javascript

```javascript

var socket = new YiiNodeSocket();
socket.on('message', function (message) {
	console.log(message.text);
	// render message
});

```
 
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
$frame = Yii::app()->nodeSocket->getFrameFactory()->createEventFrame();

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
$frame = Yii::app()->nodeSocket->getFrameFactory()->createPublicDataFrame();

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
$frame = Yii::app()->nodeSocket->getFrameFactory()->createEventFrame();

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

Only member of testRoom can catch this event

####Invoke client function or method

In your PHP application you can invoke javascript function or method of object in window context.

```php

$invokeFrame = Yii::app()->nodeSocket->getFrameFactory()->createInvokeFrame();
$invokeFrame->invokeFunction('alert', array('Hello world'));
$invokeFrame->send();	// alert will be showed on all clients

```

Extends from Event frame => you can send it into specific room

####DOM manipulations with jquery

Task: you need update price on client side after price update in each product

```php

...

$product = Product::model()->findByPk($productId);
if ($product) {
	$product->price = $newPrice;
	if ($product->save()) {
		$jFrame = Yii::app()->nodeSocket->getFrameFactory()->createJQueryFrame();
		$jFrame
			->createQuery('#product' . $product->id)
			->find('span.price')
			->text($product->price);
		$jFrame->send();
		// and all connected clients will can see updated price
	}
}

...

```

####Send more than one frame per a time

Example 1: 

```php

$multipleFrame = Yii::app()->nodeSocket->getFrameFactory()->createMultipleFrame();

$eventFrame = Yii::app()->nodeSocket->getFrameFactory()->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$dataEvent = Yii::app()->nodeSocket->getFrameFactory()->createPublicDataFrame();

$dataEvent->setKey('error.strings');
$dataEvent['key'] = $value;

$multipleFrame->addFrame($eventFrame);
$multipleFrame->addFrame($dataEvent);
$multipleFrame->send();

```

Example 2:

```php

$multipleFrame = Yii::app()->nodeSocket->getFrameFactory()->createMultipleFrame();

$eventFrame = $multipleFrame->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$dataEvent = $multipleFrame->createPublicDataFrame();

$dataEvent->setKey('error.strings');
$dataEvent['key'] = $value;

$multipleFrame->send();

```

##PS

Sorry for my english :)

