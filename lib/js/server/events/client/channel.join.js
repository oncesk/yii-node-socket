var joinClient = {

	componentManager : null,

	name : 'channel_join',

	init : function () {},

	handler : function (id, fn) {
		switch (typeof id) {

			case 'number':
			case 'string':
				joinToChannel(this, id, fn);
				return;
				break;
		}
		fn(false, 'Invalid channel id, valid id types [string,number]');
	}
};
function joinToChannel(socket, id, fn) {
	var channel = joinClient.componentManager.get('channel').channel.get(id);
	if (channel) {
		channel.join(socket, fn, true);

	} else {
		fn({
			errorMessage : 'Channel not found'
		}, true);
	}
	/*
	var room = makeRoomName(roomId);
	var roomMembersCount = joinClient.componentManager.get('io').of('/client').clients(room).length;
	joinClient.componentManager.get('io').of('/client').in(room).emit(room + ':system:room_members_count', roomMembersCount + 1);
	socket.join(room);
	fn(true, roomMembersCount + 1);
	*/
}

module.exports = joinClient;