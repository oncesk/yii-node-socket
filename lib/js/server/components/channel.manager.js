var subscribers = {};
var channels = {};
var channelByNames = {};
var subscriberChannels = {};
var channelSubscribers = {};
var uidSubscriber = {};
var sidSubscriber = {};

function createDBSubscriber(attributes, fn) {

}

function createSubscriber(attributes) {
	var subscriber = {
		id : attributes.id,
		uid : attributes.user_id,
		sid : attributes.sid,
		attributes : attributes,
		channels : {},
		init : function () {
			subscribers[this.id] = this;
			if (this.uid) {
				uidSubscriber[this.uid] = this;
			}
			if (this.sid) {
				sidSubscriber[this.sid] = this;
			}
		},
		delete : function () {
			for (var id in this.channels) {
				this.channels[id].unSubscribe(this);
			}
			this.channels = null;
			if (subscribers[this.id]) {
				delete subscribers[this.id];
				if (this.uid && uidSubscriber[this.uid]) {
					delete uidSubscriber[this.uid];
				}
				if (this.sid && sidSubscriber[this.sid]) {
					delete sidSubscriber[this.sid];
				}
			}
		}
	};
	subscriber.init();
	return subscriber;
}

function createChannel(attributes, manager) {
	var channel = {
		id : attributes.id,
		name : attributes.name,
		attributes : attributes,
		isAllowed : function (role) {
			if (this.attributes.allowed_roles) {
				for (var i in this.attributes.allowed_roles) {
					if (this.attributes.allowed_roles[i] == role) {
						return true;
					}
				}
				return false;
			}
			return true;
		},
		getAlias : function () {
			return this.attributes.properties['alias'];
		},
		subscribe : function (subscriber) {
			subscriber.channels[this.id] = this;
			var socket;
			if (subscriber.uid) {
				socket = manager.get('sp').getByUid(subscriber.uid);
			} else {
				socket = manager.get('sp').getBySid(subscriber.sid);
			}
			if (socket) {
				socket.join('channel:' + this.name);
			}
			this.subscribersNumber++;
			this.subscribers[subscriber.id] = subscriber;
			this.emit('subscribe', subscriber.attributes);
		},
		unSubscribe : function (subscriber) {
			if (subscriber.channels[this.id]) {
				delete subscriber.channels[this.id];
			}
			var socket;
			if (subscriber.attributes.uid) {
				socket = manager.get('sp').getByUid(subscriber.attributes.uid);
			} else {
				socket = manager.get('sp').getBySid(subscriber.attributes.sid);
			}
			if (socket) {
				socket.leave('channel:' + this.name);
			}
			this.subscribersNumber--;
			delete this.subscribers[subscriber.id];
			this.emit('unsubscribe', subscriber.attributes);
		},
		join : function (socket, fn, isClientSide) {
			if (this.attributes.is_authentication_required) {
				if (!socket.handshake['uid']) {
					fn({
						errorMessage : 'Authentication required'
					}, true);
				} else {
					if (this.subscribers[socket.handshake.uid]) {
						fn(this.attributes, false);
					} else {
						if (this.isAllowed(socket.handshake.session.user.role)) {
							if (isClientSide && this.attributes.subscriber_source == 1) {
								fn({
									errorMessage : 'Subscription is unavailable'
								}, true)
							} else {
								createDBSubscriber({
									user_id : socket.handshake['uid'],
									role : socket.handshake.session.user.role || 'user'
								}, function (subscriber, err) {
									if (err) {
										fn({
											errorMessage : subscriber
										}, true)
									} else {
										//  todo implement access
									}
								});
							}
						}
					}
				}
			} else {
				//  todo handle without authentication
			}
		},
		getPull : function () {
			return manager
					.get('io')
					.of('/client')
					.in('channel:' + this.name);
		},
		createDataPackage : function (data) {
			return {
				's' : {
					'alias' : this.getAlias()
				},
				'data' : data
			};
		},
		getClientEventName : function (event) {
			return 'channel:' + this.name + ':' + event;
		},
		emit : function (event, data) {
			this.getPull().emit(
				this.getClientEventName(event),
				this.createDataPackage(data)
			);
		},
		broadcast : function (event, data) {
			this.getPull().broadcast(
				this.getClientEventName(event),
				this.createDataPackage(data)
			);
		},
		subscribers : {},
		subscribersNumber : 0,
		init : function () {
			channels[this.id] = this;
			channelByNames[this.name] = this;

			if (this.attributes.allowed_roles) {
				this.attributes.allowed_roles = this.attributes.allowed_roles.split(',');
			}
		},
		delete : function () {
			for (var id in this.subscribers) {
				this.unSubscribe(this.subscribers[id]);
			}
			this.subscribers = null;
			delete channels[this.id];
			delete channelByNames[this.name];
		}
	};
	channel.init();
	return channel;
}


var manager = {

	manager : null,
	name : 'channel',
	init : function () {},

	channel : {

		get : function (id) {
			return channels[id];
		},

		getByName : function (name) {
			return channelByNames[name];
		},

		create : function (frame) {
			return createChannel(frame.data, manager.manager);
		},

		delete : function (frame) {
			var channel = this.get(frame.data.id);
			if (channel) {
				channel.delete();
			}
		}

	},
	subscriber : {
		get : function (id) {
			return subscribers[id];
		},
		create : function (frame) {
			return createSubscriber(frame.data, manager.manager);
		},
		delete : function (frame) {
			var subscriber = this.get(frame.data.id);
			if (subscriber) {
				subscriber.delete();
			}
		}
	}
};

module.exports = manager;