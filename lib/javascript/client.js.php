<?php
/**
 *
 * @var SocketTransportCommand $this
 * @var SocketTransport $socketTransport
 *
 */
?>
function YiiSocketTransport() {
	var host = '<?php echo $socketTransport->host;?>';
	var port = parseInt('<?php echo $socketTransport->port;?>');
	var namespace = '<?php echo $socketTransport->socketNamespace;?>';
	var frameType = {
		TYPE_EVENT : '<?php echo AFrame::TYPE_EVENT;?>',
		TYPE_MULTIPLE_JAVASCRIPT_EXEC : '<?php echo AFrame::TYPE_MULTIPLE_JAVASCRIPT_EXEC;?>'
	};

	var eventHandlerStack = {};

	this.on = function (event, fn) {
		if (!eventHandlerStack[event]) {
			eventHandlerStack[event] = [];
		}
		eventHandlerStack[event].push(fn);
		return this;
	};

	var self = this;

	var socket = io.connect('http://' + host + ':' + port + path);
	socket.on('socket.transport', function (frame) {
		switch (frame.type) {
			case frameType.TYPE_EVENT:
				if (eventHandlerStack[frame.event]) {
					for (var i in eventHandlerStack[frame.event]) {
						eventHandlerStack[frame.event][i].call(self, frame.data);
					}
				}
				btreak;
		}
	});
}