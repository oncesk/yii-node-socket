var subscribers = {};
var channels = {};
var channelByNames = {};
var subscriberChannels = {};
var channelSubscribers = {};
var uidSubscriber = {};
var sidSubscriber = {};
var subscriberChannelOptions = {};

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
		subscribe : function (subscriber, options, checkDbRecord, fn) {
			if (this.subscribers[subscriber.id]) {
				if (fn) {
					fn(true);
				}
				return;
			}
			var self = this;
			var options = options || {};
			var subscribe = function (options) {
				subscriber.channels[self.id] = self;
				var socket;
				if (subscriber.uid) {
					socket = manager.get('sp').getByUid(subscriber.uid);
				} else {
					socket = manager.get('sp').getBySid(subscriber.sid);
				}
				self.join(socket);
				self.subscribersNumber++;
				self.subscribers[subscriber.id] = subscriber;
				self.emit('subscribe', subscriber.attributes);
				self.subscriptionOptions[subscriber.id] = options;
				if (fn) {
					fn(true);
				}
			};
			if (checkDbRecord) {
				var subscriberChannel = manager.get('db').SubscriberChannel;
				//  check if link between subscriber and channel exists
				subscriberChannel.exists({
					channel_id : this.id,
					subscriber_id : subscriber.id
				}, function (exists, err) {
					if (err) {
						//  error
						if (fn) {
							fn(false);
						}
					} else {
						if (exists) {
							subscribe();
							fn(false, exists);
						} else {
							var link = {
								channel_id : self.id,
								subscriber_id : subscriber.id,
								can_send_event_from_php : 1,
								can_send_event_from_js : 0
							};
							subscriberChannel.create(link, function (err) {
								if (err) {
									fn(false);
								} else {
									subscribe(link);
								}
							});
						}
					}
				});
			} else {
				subscribe(options);
			}
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
			this.leave(socket);
			this.subscribersNumber--;
			delete this.subscribers[subscriber.id];
			this.emit('unsubscribe', subscriber.attributes);
		},

		join : function (socket) {
			if (socket) {
				socket.join('channel:' + this.name);
			}
		},

		leave : function (socket) {
			if (socket) {
				socket.leave('channel:' + this.name);
			}
		},

		clientLeave : function (socket, fn) {
			var self = this;

			//  try find subscriber by sid
			var subscriber = sidSubscriber[socket.handshake.sid];
			if (!subscriber && socket.handshake.uid) {
				//  if not found, try find by uid
				subscriber = uidSubscriber[socket.handshake.uid];
			}
			if (subscriber) {
				this.unSubscribe(subscriber);
				fn(true);
			} else {
				fn(false);
			}
		},

		clientJoin : function (socket, fn) {
			var self = this;

			//  try find subscriber by sid
			var subscriber = sidSubscriber[socket.handshake.sid];
			if (!subscriber && socket.handshake.uid) {
				//  if not found, try find by uid
				subscriber = uidSubscriber[socket.handshake.uid];
			}
			if (subscriber) {
				//  if subscriber exists need to check his subscription
				//  check if subscriber subscribed on this channel
				if (this.subscribers[subscriber.id]) {
					//  already subscribed
					fn(this.attributes, false);
					return;
				} else if (this.attributes.subscriber_source > 1) {
					//  if subscriber can subscribe from client side, do it!
					this.subscribe(subscriber);
					fn(this.attributes, false);
					return;
				}
				//  seems like clients can not be subscribed from javascript
				fn({
					errorMessage : 'Subscription is unavailable'
				}, true);
				return;
			} else {
				//  seems like subscriber not created
				//  hm, can this be? yes, if connection from javascript and subscriber was not created from php
				//  need to check if this channel allow subscription from javascript
				if (this.attributes.subscriber_source == 1) {
					//  allowed only from php
					fn({
						errorMessage : 'Subscription is unavailable'
					}, true);
					return;
				}
				//  check if authentication required
				if (this.attributes.is_authentication_required) {
					//  socket should be authenticated
					if (!socket.handshake.session.user.isAuthenticated) {
						//  seems like no
						fn({
							errorMessage : 'Authentication required'
						}, true);
						return;
					}
					//  need to check user role
					if (!this.isAllowed(socket.handshake.session.user.role)) {
						//  socket role not allowed
						fn({
							errorMessage : 'Subscription is unavailable'
						}, true);
						return;
					}
				}
				//  we need to create subscriber
				createDBSubscriber({
					user_id : socket.handshake.uid,
					sid : socket.handshake.sid,
					role : socket.handshake.session.user.role
				}, function (subscriber, err) {
					//  check on error
					if (err) {
						//  return error message
						fn({
							errorMessage : subscriber
						}, true);
						return;
					}
					//  successfully created
					//  subscribe
					self.subscribe(subscriber);
					//  return channel information
					fn(self.attributes, false);
				});
			}
		},
		getPull : function () {
			return manager
					.get('io')
					.of('/client')
					.in('channel:' + this.name);
		},
		getClientEventName : function (event) {
			return 'channel:' + this.name + ':' + event;
		},
		emit : function (event, data) {
			this.getPull().emit(
				this.getClientEventName(event),
				data
			);
			this._sendAlias(event, data);
		},
		broadcast : function (event, data) {
			this.getPull().broadcast(
				this.getClientEventName(event),
				data
			);
			this._sendAlias(event, data);
		},
		_sendAlias : function (event, data) {
			var alias = this.getAlias();
			if (alias) {
				this.getPull().emit(
					'alias:' + alias + ':' + event,
					{
						sender : this.attributes,
						data : data
					}
				);
			}
		},
		subscribers : {},
		subscriptionOptions : {},
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

		create : function (attributes) {
			return createChannel(attributes, manager.manager);
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

		getBySid : function (sid) {
			return sidSubscriber[sid];
		},

		getByUid : function (uid) {
			return uidSubscriber[uid];
		},

		create : function (attributes) {
			return createSubscriber(attributes, manager.manager);
		},

		delete : function (frame) {
			var subscriber = this.get(frame.data.id);
			if (subscriber) {
				subscriber.delete();
			}
		}
	},

	attachToChannels : function (socket) {
		var subscriber = this.subscriber.getBySid(socket.handshake.sid);
		if (!subscriber && socket.handshake.uid) {
			subscriber = this.subscriber.getByUid(socket.handshake.uid);
		}
		if (subscriber) {
			//  if subscriber found
			//  join it to subscribed channels
			for (var id in subscriber.channels) {
				subscriber.channels[id].join(socket);
			}
		}
	}
};

module.exports = manager;