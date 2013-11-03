var multipleFrame = {

	componentManager : null,

	name : 'multi_frame',

	init : function () {},

	handler : function (frame) {
		var handlers = multipleFrame.componentManager.get('eventManager').server.getEvents();
		for (var event in frame.meta.data) {
			if (handlers[event]) {
				for (var id in frame.meta.data[event]) {
					handlers[event].handler.call(this, {
						meta : frame.meta.data[event][id],
						data : frame.data[event][id]
					});
				}
			}
		}
	}
};

module.exports = multipleFrame;