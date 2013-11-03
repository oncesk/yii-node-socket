var data = {};
module.exports = {

	manager : null,

	name : 'publicData',

	init : function () {},

	get : function (key) {
		return data[key];
	},

	set : function (key, value, lifetime) {
		data[key] = value;
		if (lifetime > 0) {
			setTimeout(function () {
				if (data[key]) {
					delete data[key];
				}
			}, lifetime * 1000);
		}
	}
};