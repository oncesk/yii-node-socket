var connector = {

	init : function (options) {
		return true;
	},

	channel : {
		count : function (fn) {
			fn('This is a dummy connector');
		},

		get : function (fn) {
			fn('This is a dummy connector');
		}
	},
	subscriber : {

	},
	channelSubscriber : {

	}
};
module.exports = connector;