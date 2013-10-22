yii-ext-socket-transport
=================

Реализует связь между php и client javascript по средству сокет соединения.
Сокет сервер реализован на основе nodejs (socket.io library).

###Добавления события в систему? Легко!

Для добавления нового события в систему нет необходимости перезагружать nodejs сервер и ничего не надо туда дописывать. На стороне PHP необходимо отправить событие (+ данные), а на стороне клиента его отловить и выполнить какието действия.

#Установка

1. Необходимо установить nodejs (в этом вам поможет http://nodejs.org/)
2. Необходимо клонировать или сделать pull проекта (обычно делаю это в директорию application.ext)
3. Зайди в директорию, куда было установлено рассширени и выполнить<br>
`git submodule init`
<br>
`git submodule update`
<br>
4. Необходимо добавить путь к консольной команде в конфиг (console.php) приложения.
Сделать это можно добавивь следующие строки
<br>
```
'commandMap' => array(
		'socketTransport' => 'application.extensions.socket-transport.lib.php.SocketTransportCommand'
)
```
**socket-transport** - директория в которой находится расширение
<br>
5. Зайди в директорию lib/nodejs и выполнить в консоле команду установки socket.io расширения
`npm install socket.io`
6. Добавить расширения как компонент в конфигурационные файлы **main.php и console.php**, указав в качестве компонент **ext.socket-transport.SocketTransport**

> Обратите внимание на то, что если название компонента будет отличным от **socket-transport**, то придется передавать название компонента в команду используя ключ --componentName=название_компонента

###Nodejs socket.io server configuration

You need install nodejs socket.io library into lib/nodejs folder by command below

>*npm install socket.io*
