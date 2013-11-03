function Channel(id, subscription) {

	var sidCollection = {};

	this.hasSid = function (sid) {
		if (sidCollection[sid]) {
			return true;
		}
		return false;
	};

	this.joinSocket = function (socket) {
		if (this.hasSid(socket.sid)) {
			socket.join(this.getId());
		}
		return this;
	};

	this.getId = function () {
		return id;
	};

	this.subscribe = function (sid) {
		if (!this.hasSid(sid)) {
			sidCollection[sid] = {
				createData : new Date().getTime()
			};
		}
		return this;
	};

	this.unSubscribe = function (sid) {
		if (this.hasSid(sid)) {
			delete sidCollection[sid];
		}
		return this;
	};
}

module.exports = {

	createChannel : function (id) {
		return new Channel(id);
	}
};