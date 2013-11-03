var data = {};

var publicData = {

	getData : function (key) {
		var isValidKey = false;
		switch (typeof key) {
			case 'number':
			case 'string':
				key = [key];
				return data[key];
				break;

			case 'object':
				isValidKey = true;
				break;
		}
		if (isValidKey) {
			var response = {};
			for (var i in key) {
				response[key[i]] = data[key[i]];
			}
			return response;
		}
		return null;
	},

	setData : function (key, value, lifetime) {
		data[key] = value;
		if (lifetime && typeof lifetime == 'number' && lifetime > 0) {
			var self = this;
			setTimeout(function () {
				if (data[key]) {
					delete data[key];
				}
			}, lifetime * 1000);
		}
	}
};

module.exports = publicData;