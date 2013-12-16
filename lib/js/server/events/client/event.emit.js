var public_data = {

	componentManager : null,

	name : 'event:emit',

	init : function () {},

	handler : function (packet) {
		if (packet['event'] && packet['scope'] && packet['scope']['type']) {
			if (packet['scope']['type'] == 'global') {
				//  global event -> for all clients
				public_data.componentManager.get('io').of('/client').emit(
					'global:' + packet['event'],
					packet['data'] || {}
				);
			} else if (packet['scope']['type'] == 'room' && packet['scope']['id']) {
				//  room event -> for sockets in concrete room
				public_data.componentManager.get('io').of('/client').in('room:' + packet['scope']['id']).emit(
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