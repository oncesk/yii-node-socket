var fs = require('fs');
var components = {};

var manager = {

	/**
	 *
	 * @param name
	 * @param component
	 */
	set : function (name, component) {
		components[name] = component;
	},

	/**
	 *
	 * @param name
	 * @returns {*}
	 */
	get : function (name) {
		return components[name];
	}
};

var files = fs.readdirSync('./components/');
for (var i in files) {
	var component = require('./components/' + files[i]);
	if (component && component.name) {
		component.manager = manager;
		component.init();
		manager.set(component.name, component);
	}
}

module.exports = manager;