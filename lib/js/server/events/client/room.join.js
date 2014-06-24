var joinClient = {

	componentManager : null,

	name : 'room_join',

	init : function () {},

	handler : function (id, fn) {
		console.log('Tying connect socket to room');
		switch (typeof id) {

			case 'number':
			case 'string':
				console.log('Room ' + id);
				joinToRoom(this, id, fn);
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
							console.log('Room ' + id[i]);
							var room = makeRoomName(id[i]);
							var len = joinClient.componentManager.get('io').of('/client').clients(room).length;
							joinClient.componentManager.get('io').of('/client').in(room).emit(room + ':room:system:update.members_count', len + 1);
							this.join(room);
							console.log('Socket connected to ' + room);
							isJoined[id[i]] = true;
							numberOfRoomClients[id[i]] = len + 1;
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

function makeRoomName(id) {
	return 'room:' + id;
}

function joinToRoom(socket, roomId, fn) {
	var room = makeRoomName(roomId);
	var roomMembersCount = joinClient.componentManager.get('io').of('/client').clients(room).length;
	joinClient.componentManager.get('io').of('/client').in(room).emit(room + ':system:room_members_count', roomMembersCount + 1);
	socket.join(room);
	console.log('Socket connected');
	fn(true, roomMembersCount + 1);
}

module.exports = joinClient;