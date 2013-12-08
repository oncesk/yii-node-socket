var ev = require('events');
var util = require("util");

var subscribers = {};
var channels = {};
var subscriberChannels = {};
var channelSubscribers = {};

function Store(store) {
	this._storage = store;
}
Store.prototype.get = function (id) {
	return this._storage[id];
};
Store.prototype.add = function (item) {
	this._storage[item.id] = item;
};
Store.prototype.remove = function (item) {
	if (this._storage[item.id]) {
		delete this._storage[item.id];
	}
};
Store.prototype.destroy = function () {
	delete this._storage;
};

function Subscriber(attributes) {
	ev.EventEmitter.call(this);

	this.id = attributes.id;
	this.attributes = attributes;

	subscriberChannels[this.id] = {};

	this.store = new Store(subscriberChannels[this.id]);
}
util.inherits(Subscriber, ev.EventEmitter);

Subscriber.prototype.delete = function () {
	this.store.destroy();
	delete subscriberChannels[this.id];
	this.emit('delete');
};

function Channel(attributes) {
	ev.EventEmitter.call(this);

	this.id = attributes.id;
	this.name = attributes.name;
	this.attributes = attributes;

	channelSubscribers[this.id] = {};

	this._addSubscriber = function (subscriber) {
		channelSubscribers[this.id][subscriber.id] = subscriber;
	};

	this._removeSubscriber = function (subscriber) {
		if (channelSubscribers[this.id][subscriber.id]) {
			delete channelSubscribers[this.id][subscriber.id];
		}
	};
}
util.inherits(Channel, ev.EventEmitter);

Channel.prototype.update = function (attributes) {
	this.attributes = attributes;
	this.id = attributes.id;
	this.name = attributes.name;
};

Channel.prototype.getAlias = function () {
	return this.attributes.properties['alias'];
};

Channel.prototype.getSubscribers = function () {
	return channelSubscribers[this.id];
};

Channel.prototype.subscriber = function (subscriber) {
	var self = this;
	subscriber.on('delete', function () {
		self.unSubscribe(this);
	});
	this._addSubscriber(subscriber);
	this.emit('subscribe', subscriber);
};

Channel.prototype.unSubscribe = function (subscriber) {
	this._removeSubscriber(subscriber);
	this.emit('unsubscribe', subscriber);
};

Channel.prototype.delete = function () {
	for (var id in channelSubscribers) {
		this.unSubscribe(channelSubscribers[id]);
	}
	this.emit('delete');
};

function Manager() {

	ev.EventEmitter.call(this);
}
util.inherits(Manager, ev.EventEmitter);

function ChannelManager() {
	ev.EventEmitter.call(this);
}
util.inherits(ChannelManager, Manager);

ChannelManager.prototype.create = function (frame) {
	var attributes = frame.attributes;
	if (channels[attributes.id]) {
		channels[attributes.id].update(attributes);
		return channels[attributes.id];
	}
	var channel = new Channel(attributes);
	channels[channel.id] = channel;
	return channel;
};

ChannelManager.prototype.delete = function (frame) {
	if (channels[frame.data.id]) {
		channels[frame.data.id].delete();
		delete channels[frame.data.id];
	}
};

ChannelManager.prototype.subscribe = function (frame) {
	if (channels[frame.data.channel_id]) {
		if (subscribers[frame.data.subscriber_id]) {
			channels[frame.data.channel_id].subscribe(subscribers[frame.data.subscriber_id]);
		}
	}
};

ChannelManager.prototype.unSubscribe = function (frame) {
	if (channels[frame.data.channel_id]) {
		if (subscribers[frame.data.subscriber_id]) {
			channels[frame.data.channel_id].unSubscribe(subscribers[frame.data.subscriber_id]);
		}
	}
};

function SubscriberManager() {
	ev.EventEmitter.call(this);
}
util.inherits(SubscriberManager, Manager);

SubscriberManager.prototype.create = function (frame) {
	var attributes = frame.data;
	if (subscribers[attributes.id]) {
		subscribers[attributes.id].update(attributes);
		return subscribers[attributes.id];
	}
	var subscriber = new Subscriber(attributes);
	subscribers[subscriber.id] = subscriber;
	return subscriber;
};

SubscriberManager.prototype.delete = function (frame) {
	if (subscribers[frame.data.id]) {
		subscribers[frame.data.id].delete();
	}
};

var manager = {

	manager : null,
	name : 'channel',
	init : function () {},

	channel : new ChannelManager(),
	subscriber : new SubscriberManager()
};

module.exports = manager;