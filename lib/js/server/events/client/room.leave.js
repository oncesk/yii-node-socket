var leaveClient = {

	componentManager : null,

	name : 'room_leave',

	init : function () {},

	handler : function (id, fn) {
		console.log('Tying leave socket from room ' + id);
		if (typeof id == 'string') {
			this.leave('room:' + id);
		}
		if (fn) {
			fn(true);
		}
	}
};

module.exports = leaveClient;