yii-ext-socket-transport
=================

Реализует связь между php и client javascript по средству сокет соединения.
Сокет сервер реализован на основе nodejs (socket.io library).

###Добавления события в систему? Легко!

Для добавления нового события в систему нет необходимости перезагружать nodejs сервер и ничего не надо туда дописывать. На стороне PHP необходимо отправить событие (+ данные), а на стороне клиента его отловить и выполнить какието действия.

#Установка

1. Необходимо установить nodejs (в этом вам поможет http://nodejs.org/)
2. Скопируйте содержимое в папку application.ext
3. Зайди в директорию, куда было установлено рассширени и выполнить<br>
`git submodule init`
<br>
`git submodule update`
<br>
4. Необходимо добавить путь к консольной команде в конфиг (console.php) приложения.
Сделать это можно добавивь следующие строки
<br>
```php
'commandMap' => array(
		'socketTransport' => 'application.extensions.socket-transport.lib.php.SocketTransportCommand'
)
```
**socket-transport** - директория в которой находится расширение
<br>
5. Зайди в директорию application.path_to_yii_ext_socket_transport.lib.nodejs и выполнить в консоле команду установки socket.io расширения
`npm install socket.io`
6. Добавить расширения как компонент в конфигурационные файлы **main.php и console.php**, указав в качестве класса компонент **ext.socket-transport.SocketTransport**

> Обратите внимание на то, что если название компонента будет отличным от **socketTransport**, то придется передавать название компонента в команду используя ключ --componentName=название_компонента

###Запуск сокет сервера

Сервер запускается консольной коммандой Yii (**./yiic socketTransport**)

`./yiic socketTransport` покажет возможные действия команды
<br>
`./yiic socketTransport start` запуск сокет сервера

###Сторона клиента

Для возможности добавлять слушателей событий необходимо зарегестрировать скрипт расширения, делается это следующим образом

> Где то в php<br>

```php
Yii::app()->socketTransport->registerClientScripts();
```

> Javscript клиента

```javascript
var listener = new YiiSocketTransport();
```

Default events:

* `listener.on('connect', function () {})` - "connect" is emitted when the socket connected successfully
* `listener.on('reconnect', function () {})` - "reconnect" is emitted when socket.io successfully reconnected to the server
* `listener.on('disconnect', function () {})` - "disconnect" is emitted when the socket disconnected

Custom events:

* `listener.on('update', function (data) {})` - emitted when PHP server emit update event
* `listener.on('some_event', function (data) {})` - emitted when PHP server emit some_event event

###Отправка события клиенту из PHP

PHP:
```php
$frame = Yii::app()->socketTransport->createEventFrame();
$frame
	->setEventName('update.queue')
	->setData(array(
		array('client' => 'A100', 'table' => '10')
	))
	->send();

```

JS:
```javascript
var listener = new YiiSocketTransport();
listener.on('update.queue', function (data) {
	for (var i in data) {
		updateBoard(data[i]);	//	update queue board
	}
});
```
