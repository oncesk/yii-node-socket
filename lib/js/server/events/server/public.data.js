var public_data = {

	componentManager : null,

	name : 'public_data',

	init : function () {},

	handler : function (frame) {
		public_data.componentManager.get('publicData').set(frame.meta.data.key, frame.data, frame.meta.data.lifetime);
	}
};

module.exports = public_data;