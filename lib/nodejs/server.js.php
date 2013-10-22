<?php
/**
 * @var SocketTransport $socketTransport
 * @var SocketTransportCommand $this
 */
?>
var host = '<?php echo $socketTransport->host;?>';
var port = parseInt('<?php echo $socketTransport->port;?>');
var frameType = {
	TYPE_EVENT : '<?php echo AFrame::TYPE_EVENT;?>',
	TYPE_MULTIPLE_JAVASCRIPT_EXEC : '<?php echo AFrame::TYPE_MULTIPLE_JAVASCRIPT_EXEC;?>'
};
if (port === undefined || host === undefined) {
	console.log("Invalid - host or port");
	process.exit(1);
}
var http = require('http');

var server = http.createServer().listen(port, host);
var io = require('socket.io').listen(server);

io.set('transports', [
	'websocket'
	, 'flashsocket'
	, 'htmlfile'
	, 'xhr-polling'
	, 'jsonp-polling'
]);

io.set('log level', 3);

io.on('connection', function (socket) {

	socket.on('socket.transport', function (frame) {
		if (frame.type == frameType.TYPE_MULTIPLE_JAVASCRIPT_EXEC) {
			if (frame.exec.server.length > 0) {
				for (var i in frame.exec.server) {
					eval(frame.exec.server[i]);
				}
			}
			delete frame.exec['server'];
		}
		socket.broadcast.emit('socket.transport', frame);
	});

	socket.on('socket.transport.client', function (frame) {

	})
});