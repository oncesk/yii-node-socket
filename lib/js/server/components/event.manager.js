var fs = require('fs');
var componentManager = require( __dirname + '/component.manager.js');

var events = {
	client : {},
	server : {}
};

var manager = {

	client : {

		/**
		 *
		 * @returns {{}}
		 */
		getEvents : function () {
			return events.client;
		},

		/**
		 *
		 * @param socket
		 */
		bind : function (socket) {
			//  add event listeners to socket
			var ev = this.getEvents();
			for (var name in ev) {
				socket.on(name, ev[name].handler);
			}
		}
	},

	server : {

		/**
		 *
		 * @returns {{}}
		 */
		getEvents : function () {
			return events.server;
		},

		/**
		 *
		 * @param socket
		 */
		bind : function (socket) {
			//  add event listeners to socket
			var ev = this.getEvents();
			for (var name in ev) {
				socket.on(name, ev[name].handler);
			}
		}
	}
};

var setEvents = function (namespace) {
	var files = fs.readdirSync(__dirname + '/../events/' + namespace);
	for (var i in files) {
		var ev = require('./../events/' + namespace + "/" + files[i]);
		if (ev) {
			ev.componentManager = componentManager;
			ev.init();
			events[namespace][ev.name] = ev;
		}
	}
};
setEvents('server');
setEvents('client');

module.exports = manager;