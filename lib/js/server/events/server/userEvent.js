var userEvent = {

	componentManager : null,

	name : 'userEvent',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['uid']) {
			var sockets = [];
			var sp = userEvent.componentManager.get('sp');
			var sessionSockets = null;
			for (var i in frame.meta.data['uid']) {
				if (sessionSockets = sp.getByUid(frame.meta.data['uid'][i])) {
					for (var id in sessionSockets) {
						sockets.push(sessionSockets[id]);
					}
				}
			}
			if (sockets.length > 0) {
				var sendEvent = function (socket) {
					if (frame.meta.data['room']) {
						socket.emit('room:' + frame.meta.data['room'] + ':' + frame.meta.data.event, frame.data);
					} else if (frame.meta.data['channel']) {
						socket.emit('channel:' + frame.meta.data['channel'] + ':' + frame.meta.data.event, frame.data);
					} else {
						socket.emit('global:' + frame.meta.data.event, frame.data);
					}
				};
				for (var i in sockets) {
					sendEvent(sockets[i]);
				}
			}
		}
	}
};

module.exports = userEvent;