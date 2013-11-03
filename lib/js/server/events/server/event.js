var event = {

	componentManager : null,

	name : 'event',

	init : function () {},

	handler : function (frame) {
		event.componentManager.get('io').of('/client').emit(frame.meta.data.event, frame.data);
	}
};

module.exports = event;