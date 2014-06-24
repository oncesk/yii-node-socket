var joinClient = {

	componentManager : null,

	name : 'channel_leave',

	init : function () {},

	handler : function (id, fn) {
		console.log('Tying leave client from channel ' + id);
		if (typeof id == 'string' && id.length > 0) {
			var channel = joinClient.componentManager.get('channel').channel.getByName(id);
			if (channel) {
				console.log('Leave from channel ' + id);
				channel.clientLeave(this, function (success) {
					if (fn) {
						fn(success);
					}
				});
			}
		}
	}
};
function joinToChannel(socket, id, fn) {
	var channel = joinClient.componentManager.get('channel').channel.getByName(id);
	if (channel) {
		console.log(socket.handshake);
		channel.clientJoin(socket, fn);
	} else {
		fn({
			errorMessage : 'Channel not found'
		}, true);
	}
}

module.exports = joinClient;