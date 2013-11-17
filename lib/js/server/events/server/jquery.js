var invoke = {

	componentManager : null,

	name : 'jquery',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['room']) {
			invoke
				.componentManager
				.get('io')
				.of('/client')
				.in('room:' + frame.meta.data['room'])
				.emit('system:jquery', frame.data);
		} else if (frame.meta.data['channel']) {
			//  todo need release channel event handling
		} else {
			invoke.componentManager.get('io').of('/client').emit('system:jquery', frame.data);
		}
	}
};

module.exports = invoke;