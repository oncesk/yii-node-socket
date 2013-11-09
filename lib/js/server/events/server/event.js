var event = {

	componentManager : null,

	name : 'event',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['room']) {
			event
				.componentManager
				.get('io')
				.of('/client')
				.in('room:' + frame.meta.data['room'])
				.emit(frame.meta.data['room'] + ':' + frame.meta.data.event, frame.data);
		} else if (frame.meta.data['channel']) {
			//  todo need release channel event handling
		} else {
			event.componentManager.get('io').of('/client').emit(frame.meta.data.event, frame.data);
		}
	}
};

module.exports = event;