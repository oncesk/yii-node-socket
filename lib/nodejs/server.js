/**
 * Created with JetBrains PhpStorm.
 * User: once
 * Date: 10/21/13
 * Time: 5:43 PM
 * To change this template use File | Settings | File Templates.
 */
var len = process.argv.length - 1;
var port = process.argv[len];
var host = process.argv[len - 1];
if (port === undefined ||
		port.indexOf('socket.js') >= 0 ||
		host === undefined ||
		host.indexOf('socket.js') >= 0) {

	console.log("Invalid arguments:\n - host should be first\n - port must be last argument");
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