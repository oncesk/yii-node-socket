var public_data = {

	componentManager : null,

	name : 'public_data',

	init : function () {},

	handler : function (key, fn) {
		fn(
			public_data.componentManager.get('publicData').get(key)
		);
	}
};

module.exports = public_data;