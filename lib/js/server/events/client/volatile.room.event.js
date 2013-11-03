function makeRoomName(id) {
	return 'volatile:room:' + id;
}
var joinClient = {

	componentManager : null,

	name : 'volatile_room_event',

	init : function () {},

	handler : function (id, fn) {
		switch (typeof id) {

			case 'number':
			case 'string':
				var room = makeRoomName(id);
				this.join(room);
				fn(true, joinClient.componentManager.get('io').of('/client').clients(room).length);
				return;
				break;

			case 'object':
				var isJoined = {};
				var numberOfRoomClients = {};
				var numberOfSuccess = 0;
				for (var i in id) {
					if (id[i]) {
						var type = typeof id[i];
						if (type == 'string' || type == 'number') {
							var room = makeRoomName(id);
							this.join(room);
							isJoined[id[i]] = true;
							numberOfRoomClients[id[i]] = joinClient.componentManager.get('io').of('/client').clients(room).length;
							numberOfSuccess++;
						}
					}
				}
				if (numberOfSuccess) {
					fn(isJoined, numberOfRoomClients);
					return;
				}
				fn(false, 'Invalid or empty channel id (valid id types [string,number,array,object])');
				return;
				break;
		}
		fn(false, 'Invalid channel id, valid id types [string,number,array,object]');
	}
};

module.exports = joinClient;