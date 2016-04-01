var public_data = {

	componentManager : null,

	name : 'event:emit',

	init : function () {},

	handler : function (packet) {
		if (packet['event'] && packet['scope'] && packet['scope']['type']) {
			var client = public_data.componentManager.get('io').of('/client');
			if (packet['scope']['type'] == 'global') {
				if (packet['broadcast'] === true) {
					//  global event -> for all clients excluding sender
					client.except(this.id).emit(
						'global:' + packet['event'],
						packet['data'] || {}
					);
				} else {
					//  global event -> for all clients including sender
					client.emit(
						'global:' + packet['event'],
						packet['data'] || {}
					);
				}
			} else if (packet['scope']['type'] == 'room' && packet['scope']['id']) {
				//  room event -> for sockets in concrete room
				client.in('room:' + packet['scope']['id']).emit(
					'room:' + packet['scope']['id'] + ':' + packet['event'],
					packet['data'] || {},
					true
				);
			} else if (packet['scope']['type'] == 'channel') {
				//  todo implement channel event
			}
		}
	}
};

module.exports = public_data;