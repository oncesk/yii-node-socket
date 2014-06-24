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
				.emit('room:' + frame.meta.data['room'] + ':' + frame.meta.data.event, frame.data);
		} else if (frame.meta.data['channel']) {
			var channel = event
					.componentManager
					.get('channel')
					.channel
					.getByName(frame.meta.data['channel']);
			if (channel) {
				channel.emit(frame.meta.data.event, frame.data);
			} else {
				console.log('ERROR: channel ' + frame.meta.data['channel'] + ' not found');
			}
		} else {
			event.componentManager.get('io').of('/client').emit('global:' + frame.meta.data.event, frame.data);
		}
	}
};

module.exports = event;