var joinClient = {

	componentManager : null,

	name : 'room_join',

	init : function () {},

	handler : function (id, fn) {
		console.log('Tying leave socket from room ' + id);
		if (typeof id == 'string') {
			this.leave('room:' + id);
		}
		fn(true);
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