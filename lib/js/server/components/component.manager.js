var fs = require('fs');
var events = require("events");
var util = require("util");
var components = {};

function Manager() {
	events.EventEmitter.call(this);
}
util.inherits(Manager, events.EventEmitter);

Manager.prototype.set = function (name, component) {
	components[name] = component;
	component.cm = this;
	if (component['onComponentMangerSet']) {
		component['onComponentMangerSet']();
	}
	this.emit('set.component', name);
	return this;
};

Manager.prototype.get = function (name) {
	return components[name];
};

Manager.prototype.initCompleted = function () {
	this.emit('init.completed');
};

var manager = new Manager();
var files = fs.readdirSync( __dirname + '/autoload/' );
for (var i in files) {
	var component = require('./autoload/' + files[i]);
	if (component && component.name) {
		component.manager = manager;
		component.init();
		manager.set(component.name, component);
	}
}

module.exports = manager;