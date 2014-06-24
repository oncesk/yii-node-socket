var invoke = {

	componentManager : null,

	name : 'jquery',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['room']) {
			invoke
				.componentManager
				.get('io')
				.of('/client')
				.in('room:' + frame.meta.data['room'])
				.emit('system:jquery', frame.data);
		} else if (frame.meta.data['channel']) {
			var channel = event
					.componentManager
					.get('channel')
					.channel
					.getByName(frame.meta.data['channel']);
			if (channel) {
				channel.emit('system:jquery', frame.data);
			} else {
				console.log('ERROR: channel ' + frame.meta.data['channel'] + ' not found');
			}
		} else {
			invoke.componentManager.get('io').of('/client').emit('system:jquery', frame.data);
		}
	}
};

module.exports = invoke;