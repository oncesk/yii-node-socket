Yii Node Socket
=================

Реализует связь между php и javascript по средству сокет соединения.
Сокет сервер реализован на nodejs (socket.io library http://socket.io/).

#Установка

Установите nodejs, если не установлено (в этом вам поможет http://nodejs.org/)<br>
Установка расширения

 * Клонирование

```bash
$> git clone git@github.com:oncesk/yii-node-socket.git
```
 * В качестве submodule<br>

> ext_directory - директория куда следует установить сабмодуль

```bash
$> git submodule add git@github.com:oncesk/yii-node-socket.git ext_directory
$> git submodule update
```
Зайдите в директорию установленного расширения ***application.ext.yii-node-socket*** и выполнить<br>
```bash
$> git submodule init
$> git submodule update
```

Настраиваем Yii<br>
 * Добавить путь к консольной команде в конфиг (***main/console.php***). Сделать это можно добавивь следующие строки:

```php
'commandMap' => array(
	'node-socket' => 'application.extensions.yii-node-socket.lib.php.NodeSocketCommand'
)
```

 * Регистрация в компонентах Yii, добавив в **main.php и console.php**:

```php
'nodeSocket' => array(
	'class' => 'application.extensions.yii-node-socket.lib.php.NodeSocket',
	'host' => 'localhost',	// по умолчанию 127.0.0.1, может быть как ip так и доменом, только без http
	'port' => 3001		// по умолчанию 3001, должен быть целочисленным integer-ом
)
```
> Notice: ***host*** - имя домена, как в вашем виртуальном хосте либо айпи адресс сервера, если вы обращаетесь к серверу через айпи адресс

Установим компоненты ***nodejs*** в директории ***application.ext.yii-node-socket.lib.js.server***:
```bash
$> npm install
```

На этом установка окончена!

> Обратите внимание на то, что если название компонента будет отличным от **nodeSocket**, то придется передавать название компонента в команду используя ключ --componentName=название_компонента

###Запуск сервера

Сервер запускается консольной коммандой Yii (**./yiic node-socket**)

```bash
$> ./yiic node-socket # выведет хелп
$> ./yiic node-socket start # запуска сервера
$> ./yiic node-socket stop # остановка сервера
$> ./yiic node-socket restart # рестарт сервера
$> ./yiic node-socket getPid # выведет pid nodejs процесса
```

##Javascript

Перед работой необходимо зарегестировать скрипты клиента на необходимой нам странице

```php
public function actionIndex() {
	// регестрируем скрипты клиенат
	Yii::app()->nodeSocket->registerClientScripts();
	
	// выполнение других действий
}
```

###События

Предустановленные события:

* `listener.on('connect', function () {})` - "connect" is emitted when the socket connected successfully
* `listener.on('reconnect', function () {})` - "reconnect" is emitted when socket.io successfully reconnected to the server
* `listener.on('disconnect', function () {})` - "disconnect" is emitted when the socket disconnected

Созднаия своего слушателя события:

* `listener.on('update', function (data) {})` - emitted when PHP server emit update event
* `listener.on('some_event', function (data) {})` - emitted when PHP server emit some_event event

###Работа в javascript

Работа с расширением происходить через класс `YiiNodeSocket`

####Начало работы

```javascript

// создаем обьект для работы с библиотекой
var socket = new YiiNodeSocket();

// включение режима отладки
socket.debug(true);
```

####События

Сейчас события можно создавать только из PHP. Данные которые приходят в javascript передаются в формате json.
В javascript данные приходят уже в обычном виде (обьекте)

```javascript
// установка обработчика события
socket.on('updateBoard', function (data) {
	// обновляем доску, выполняем какие либо действия
});
```

####Подключение к комнате

```javascript
socket.room('testRoom').join(function (success, numberOfRoomSubscribers) {
	// success - boolean, если при добавлении сокета произошла ошибка
	// то success = false, и numberOfRoomSubscribers - содержит описание ошибки
	// иначе numberOfRoomSubscribers - number, содержит количество клиентов в комнате
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

####Общие публичные данные

Данные можно установить только из PHP используя PublicData фрейм (смотрите ниже), получить данные можно используя метод `getPublicData(string key, callback fn)`

```javascript
socket.getPublicData('error.strings', function (strings) {
	// проверка обязательна, так как данные могут быть неустановлены,
	// либо истек срок хранения
	if (strings) {
		// делаем что-либо
	}
});
```


##PHP

####Регистрация клиентских скриптов

```php
public function actionIndex() {
	// регестрируем скрипты клиенат
	Yii::app()->nodeSocket->registerClientScripts();
	
	// выполнение других действий
}
```

####Создание и отправка события с данными

```php

...

// создаем фрейм
$frame = Yii::app()->nodeSocket->createEventFrame();
$frame->setEventName('updateBoard');
$frame['boardId'] = 25;
$frame['boardData'] = $html;
$frame->send();

...

```

####Установка общих публичных данных

Данные могут устанавливаться на какоето время, для установки времени жизни необходимо воспользоваться методом ***setLifeTime(integer $lifetime)*** класса PublicData

```php

...

// создаем фрейм
$frame = Yii::app()->nodeSocket->createPublicDataFrame();

// устанавливаем ключ в хранилище
$frame->setKey('error.strings');

// записываем данные
$frame->setData($errorStrings);

// можно устанавливать с помощью интерфейса ArrayAccess
// $frame['empty_name'] = 'Пожалуйста введите имя пользователя';

// устанавливаем время жизни
$frame->setLifeTime(3600*2);	// через два часа данные будут недоступны для использования

// отправляем
$frame->send();

...

```

####Отправка события в комнату

```php

...

// создаем фрейм
$frame = Yii::app()->nodeSocket->createEventFrame();

// устанавливаем ключ в хранилище
$frame->setEventName('updateBoard');

// записываем данные
$frame->setRoom('testRoom');

// устанавливаем данные

$frame['key'] = $value;

// отправляем
$frame->send();

...

```

Событие смогут отловить клиенты, которые состоят в комнате testRoom

####Вызов функции или метода обьекта в контексте window

```php
$invokeFrame = Yii::app()->nodeSocket->createInvokeFrame();
$invokeFrame->invokeFunction('alert', array('Hello world'));
$invokeFrame->send();   // выполнится у всех клиентов
```

Является наследником Event, что позволяет отправить событие в комнату

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
