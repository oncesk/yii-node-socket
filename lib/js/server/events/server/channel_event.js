var event = {

	componentManager : null,

	name : 'channel_event',

	init : function () {},

	handler : function (frame) {
		console.log(frame.meta.data);
		if (event.actions[frame.meta.data.action]) {
			event.actions[frame.meta.data.action](frame);
		}
		/*
		if (frame.meta.data['room']) {
			event
					.componentManager
					.get('io')
					.of('/client')
					.in('room:' + frame.meta.data['room'])
					.emit(frame.meta.data['room'] + ':' + frame.meta.data.event, frame.data);
		} else if (frame.meta.data['channel']) {
			//  todo need release channel event handling
		} else {
			event.componentManager.get('io').of('/client').emit(frame.meta.data.event, frame.data);
		}
		*/
	},

	actions : {

		'save.YiiNodeSocket\\Models\\Channel' : function (frame) {
			event.componentManager.get('channel').channel.create(frame);
		},

		'delete.YiiNodeSocket\\Models\\Channel' : function (frame) {
			event.componentManager.get('channel').channel.delete(frame);
		},

		'save.YiiNodeSocket\\Models\\Subscriber' : function (frame) {
			event.componentManager.get('channel').subscriber.create(frame);
		},

		'delete.YiiNodeSocket\\Models\\Subscriber' : function (frame) {
			event.componentManager.get('channel').subscriber.delete(frame);
		},

		'save.YiiNodeSocket\\Models\\SubscriberChannel' : function (frame) {
			event.componentManager.get('channel').channel.subscribe(frame);
		},

		'delete.YiiNodeSocket\\Models\\SubscriberChannel' : function (frame) {
			event.componentManager.get('channel').channel.unSubscribe(frame);
		}
	}
};

module.exports = event;