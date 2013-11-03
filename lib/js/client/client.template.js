function YiiSocketTransport() {

	this.debug = false;

	var self = this;
	var events = {
		before : {},
		after : {}
	};

	var writeDebugMessage = function (message) {
		if (self.debug) {
			console.log(new Date().getTime() + ': ' + message);
		}
	};

	var socket = io.connect('http://<?php echo $socketTransport->host;?>:<?php echo $socketTransport->port;?>/client');

	var eventListener = {

		trigger : function (when, method, event, data) {
			if (events[when] && events[when][method] && events[when][method][event]) {
				writeDebugMessage('Trigger event listeners for: ' + event + ', ' + method + ', ' + event);
				for (var i in events[when][method][event]) {
					if (events[when][method][event][i]) {
						events[when][method][event][i].call(self, data, event);
					}
				}
			} else {
				writeDebugMessage('No event listeners for: ' + when + ', ' + method + ', ' + event);
			}
		},

		listen : function (when, method, event, fn) {
			if (!events[when]) {
				events[when] = {};
			}
			if (!events[when][method]) {
				events[when][method] = {};
			}
			if (!events[when][method][event]) {
				events[when][method][event] = [];
			}
			events[when][method][event].push(fn);
		}
	};

	/**
	 * Attach event listener
	 *
	 * @param event
	 * @param fn
	 * @returns {*}
	 */
	this.on = function (event, fn) {
		var self = this;
		writeDebugMessage('Add event listener for: ' + event);
		socket.on(event, function (data) {
			eventListener.trigger('before', 'call', event, data);
			if (fn) {
				writeDebugMessage('Fire event: ' + event);
				fn.call(self, data);
			} else {
				writeDebugMessage('Event handler is null or undefined');
			}
			eventListener.trigger('after', 'call', event, data);
		});
		return this;
	};

	this.before = {
		call : function (event, fn) {
			eventListener.listen('before', 'call', event, fn);
			return self;
		}
	};

	this.after = {
		call : function (event, fn) {
			eventListener.listen('after', 'call', event, fn);
			return self;
		}
	};

	/**
	 * @param key
	 */
	this.getPublicData = function (key, fn) {
		writeDebugMessage('Trying to get public data for key: ' + key);
		socket.emit('public_data', key, function (data) {
			writeDebugMessage('Received public data for key: ' + key);
			if (fn) {
				fn(data);
			}
		});
	};

	this.join = function (id, fn) {
		writeDebugMessage('Trying to join socket to channel: ' + id);
		socket.emit('volatile_room_event', id, function (isJoined, numberOfClientsInRoom) {
			if (isJoined) {
				writeDebugMessage('Client successfully joined to channel: ' + id + ', new room size is: ' + numberOfClientsInRoom);
			} else {
				writeDebugMessage('Client join error: channelID' + id);
			}
			if (fn) {
				fn(isJoined, numberOfClientsInRoom, id);
			}
		});
	};
}