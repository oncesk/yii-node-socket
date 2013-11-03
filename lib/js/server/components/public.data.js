var data = {};
module.exports = {

	manager : null,

	name : 'publicData',

	init : function () {},

	get : function (key) {
		return data[key];
	},

	set : function (key, value) {
		data[key] = value;
	}
};