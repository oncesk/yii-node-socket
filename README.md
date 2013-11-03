yii-ext-socket-transport
=================

Реализует связь между php и client javascript по средству сокет соединения.
Сокет сервер реализован на nodejs (socket.io library).

#Установка

1. Необходимо установить nodejs (в этом вам поможет http://nodejs.org/)
2. Необходимо установить само расширение одним из следующих способов:<br>
a. Клонирование<br>
`$> git clone git@github.com:oncesk/yii-ext-socket-transport.git`<br>
b. Скачать архив, и распаковать его в ***application.ext.yii-ext-socket-transport***
3. Зайти в директорию установленного расширения ***application.ext.yii-ext-socket-transport*** и выполнить<br>
`$> git submodule init`
<br>
`$> git submodule update`
<br>
4. Необходимо настроить Yii<br>
a. Добавить путь к консольной команде в конфиг (main/console.php).
Сделать это можно добавивь следующие строки

```php
'commandMap' => array(
	'socketTransport' => 'application.ext.yii-ext-socket-transport.lib.php.SocketTransportCommand'
)
```

b. Зарегестрировать расширение в компонентах Yii, добавив в **main.php и console.php**:
```php
'socketTransport' => array(
	'class' => 'ext.yii-ext-socket-transport.lib.php.SocketTransport',
	'host' => '127.0.0.1',	// по умолчанию 127.0.0.1, может быть как ip так и доменом, только без http
	'port' => 3001		// по умолчанию 3001, должен быть целочисленным integer-ом
)
```
<br>
5. Необходимо зайт в директорию ***application.ext.yii-ext-socket-transport.lib.js.server*** и установить необходимые компоненты для nodejs

```bash
$> npm install
```

На этом установка окончена!

> Обратите внимание на то, что если название компонента будет отличным от **socketTransport**, то придется передавать название компонента в команду используя ключ --componentName=название_компонента

###Запуск сервера

Сервер запускается консольной коммандой Yii (**./yiic socketTransport**)

```bash
$> ./yiic socketTransport # выведет хелп
$> ./yiic socketTransport start # запуска сервера
$> ./yiic socketTransport stop # остановка сервера
$> ./yiic socketTransport restart # рестарт сервера
$> ./yiic socketTransport getPid # выведет pid nodejs процесса
```

##Javascript

Перед работой необходимо зарегестировать скрипты клиента на необходимой нам странице

```php
public function actionIndex() {
	// регестрируем скрипты клиенат
	Yii::app()->socketTransport->registerClientScripts();
	
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

Работа с расширением происходить через класс `YiiSocketTransport`

####Начало работы

```javascript

// создаем обьект для работы с библиотекой
var socket = new YiiSocketTransport();

// включение режима отладки
socket.debug = true;
```

####События

На текущем этапе разработки события можно создавать только из PHP. Данные которые приходят в javascript передаются в формате json.
В javascript данные приходят уже в обычном виде (обьекте)

```javascript
// установка обработчика события
socket.on('updateBoard', function (data) {
	// обновляем доску, выполняем какие либо действия
});
```

####Подключение к комнате

Для подключения к комнате необходимо воспользоваться методом join и передать в него идентификатор комнаты

```javascript
join : function (string,integer id, callback fn)
```

Пример:

```javascript
var roomId = 'testRoom';
socket.join(roomId, function (success, numberOfRoomSubscribers) {
	// success - boolean, если при добавлении сокета произошла ошибка
	// то success = false, и numberOfRoomSubscribers - содержит описание ошибки
	// иначе numberOfRoomSubscribers - number, содержит количество клиентов в комнате
	if (success) {
		console.log(numberOfRoomSubscribers + ' clients in room: ' + roomId);
		// do something
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
	// либо истек срок хранения (еще не реализовано)
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
	Yii::app()->socketTransport->registerClientScripts();
	
	// выполнение других действий
}
```

####Создание и отправка события с данными

```php

...

// создаем фрейм
$frame = Yii::app()->socketTransport->createEventFrame();
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
$frame = Yii::app()->socketTransport->createPublicDataFrame();

// устанавливаем ключ в хранилище
$frame->setKey('error.strings');

// записываем данные
$frame->setData($errorStrings);

// можно устанавливать с помощью интерфейса ArrayAccess
// $frame['empty_name'] = 'Пожалуйста введите имя пользователя';

// устанавливаем время жизни
$frame->setLifeTime(3600*2);	// через два часа данные удалятся из памяти

// отправляем
$frame->send();

...

```

####Отправка события в комнату

Необходимо использовать класс VolatileRoomEvent, создание и отправка события ничем неотличается от обычного события, за исключением добавления идентификатора канала

```php

...

// создаем фрейм
$frame = Yii::app()->socketTransport->createVolatileRoomEventFrame();

// устанавливаем ключ в хранилище
$frame->setEventName('updateBoard');

// записываем данные
$frame->setRoomId('testRoom');

// устанавливаем данные

$frame['key'] = $value;

// отправляем
$frame->send();

...

```

Событие смогут отловить клиенты, которые состоят в комнате testRoom

####Отправка нескольких событий за один раз

Для отправки нескольких событий используйте 

```php

$multipleFrame = Yii::app()->socketTransport->createMultipleFrame();

$eventFrame = Yii::app()->socketTransport->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$roomEvent = Yii::app()->socketTransport->createVolatileRoomEventFrame();

$roomEvent->setEventName('updateBoard');
$roomEvent->setRoomId('testRoom');
$roomEvent['key'] = $value;

$multipleFrame->addFrame($eventFrame);
$multipleFrame->addFrame($roomEvent);
$multipleFrame->send();

```

Можно и так

```php

$multipleFrame = Yii::app()->socketTransport->createMultipleFrame();

$eventFrame = $multipleFrame->createEventFrame();

$eventFrame->setEventName('updateBoard');
$eventFrame['boardId'] = 25;
$eventFrame['boardData'] = $html;

$roomEvent = $multipleFrame->createVolatileRoomEventFrame();

$roomEvent->setEventName('updateBoard');
$roomEvent->setRoomId('testRoom');
$roomEvent['key'] = $value;

$multipleFrame->send();

```

##В планах

1. Создать систему subscribe/unsibscribe, с созданием, удалением, сохранением каналов, добавления и удаления подписчиков
2. Хранение информации и канале и подписчиках в базе данных, скорее всего mongoDB, что позволит создавать каналы и подписывать клиентов лишь раз, выше отказоустойчиваость, так как данные будут храниться не в памяти а в бд, и загружать в память при старте сервера
3. Возможность создавать каналы с разными правами (публичный канал, публичный ограниченный, приватный, приватный ограниченный)
