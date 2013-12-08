var subscribers = {};
var channels = {};
var channelByNames = {};
var subscriberChannels = {};
var channelSubscribers = {};
var uidSubscriber = {};
var sidSubscriber = {};

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
			return this.name + ':' + event;
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