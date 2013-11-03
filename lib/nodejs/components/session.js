var storage = {};
function Session(sid, cookie) {

	/**
	 * @type {{}}
	 */
	var data = {};

	/**
	 * @returns {*}
	 */
	this.getSid = function () {
		return sid;
	};

	/**
	 * @param key
	 * @param value
	 */
	this.set = function (key, value) {
		data[key] = value;
	};

	/**
	 * @param key
	 * @returns {*}
	 */
	this.get = function (key) {
		return data[key];
	};

	/**
	 * @returns {boolean}
	 */
	this.isExpired = function () {
		var date = typeof cookie['expires'] == 'string' ? new Date(cookie['expires']) : cookie['expires'];
		return (new Date() < date);
	};
}

var Store = {

	createSession : function (sid, cookie) {
		return storage[sid] = new Session(sid, cookie);
	},

	get : function (sid, fn) {
		var self = this;
		process.nextTick(function () {
			if (storage[sid]) {
				if (storage[sid].isExpired()) {
					delete storage[sid];
					fn(null);
				} else {
					fn(storage[sid]);
				}
			} else {
				fn(null);
			}
		});
	}
};

module.exports = Store;