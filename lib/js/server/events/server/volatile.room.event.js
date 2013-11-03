function makeRoomName(id) {
	return 'volatile:room:' + id;
}
var event = {

	componentManager : null,

	name : 'volatile_room_event',

	init : function () {},

	handler : function (frame) {
		var room = makeRoomName(frame.meta.data.roomId);
		event.componentManager.get('io').of('/client').in(room).emit(frame.meta.data.event, frame.data);
	}
};

module.exports = event;