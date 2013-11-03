var handlers = {

	_components : {},

	getComponent : function (key) {
		return this._components[key];
	},

	setComponent : function (key, component) {
		this._components[key] = component;
	},

	/**
	 * Broadcast frame to all clients
	 *
	 * @param socket
	 * @param frame
	 */
	event : function (socket, frame) {
		//  send to all clients
		io.of('/client').broadcast.emit(frame.meta.data.event, frame.data);
	},

	/**
	 * Subscribe or unsubscribe socket from channel(s)
	 *
	 * @param socket
	 * @param frame
	 */
	subscription : function (socket, frame) {
		if (frame.meta.data.subscribe && frame.meta.data.subscribe.length > 0) {
			for (var i in frame.meta.data.subscribe) {
				subscription.getChannel(frame.meta.data.subscribe[i]).subscribe(frame.meta.sid);
			}
		}
		if (frame.meta.data.unSubscribe &&frame.meta.data.unSubscribe.length > 0) {
			for (var i in frame.meta.data.unSubscribe) {
				subscription.getChannel(frame.meta.data.unSubscribe[i]).unSubscribe(frame.meta.sid);
			}
		}
	},

	/**
	 * Handle channel frame, broadcast to all clients in some channel
	 *
	 * @param socket
	 * @param frame
	 */
	channel_event : function (socket, frame) {
		io
				.of('/client')
				.in(frame.meta.data.channel)
				.broadcast
				.emit(frame.meta.data.event, frame.data);
	},

	public_data : function (socket, frame) {
		handlers
			.getComponent('public-data')
			.setData(frame.meta.data.key, frame.data);
	},

	/**
	 * Process multi frame (one frame contains many frames)
	 *
	 * @param socket
	 * @param frame
	 */
	multi_frame : function (socket, frame) {
		for (var event in frame.meta.data) {
			if (handlers[event]) {
				for (var id in frame.meta.data[event]) {
					handlers[event].call(this, socket, {
						meta : frame.meta.data[event][id],
						data : frame.data[event][id]
					});
				}
			}
		}
	}
};

module.exports = handlers;