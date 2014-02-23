module.exports = {
	_sidPull : {},
	_uidPull : {},
	_volatileSidPull : {},
	_volatileUidPull : {},
	add : function (socket) {
		var sid = socket.handshake.sid;
		var uid = socket.handshake.uid;
		if (this._volatileSidPull[sid] && !uid) {
			uid = this._volatileSidPull[sid];
		}
		this._sidPull[sid] = socket;
		if (uid) {
			this.createSidUidLink(sid, uid);
		}
		var self = this;
		socket.on('disconnect', function () {
			if (self._sidPull[sid]) {
				if (uid && self._uidPull[uid]) {
					//  delete
					delete self._uidPull[uid];
				}
				//  delete socket link
				delete self._sidPull[sid];
			}
		});
	},
	createSidUidLink : function (sid, uid) {
		if (this._sidPull[sid]) {
			this._uidPull[uid] = sid;
			this._sidPull[sid].handshake.uid = uid;
		}
		this._volatileSidPull[sid] = uid;
		this._volatileUidPull[uid] = sid;
	},

	destroySidUidLink : function (sid, uid) {
		if (this._uidPull[uid]) {
			delete this._uidPull[uid];
		}
		if (this._volatileUidPull[uid]) {
			delete this._volatileUidPull[uid];
		}
		if (this._volatileSidPull[sid]) {
			delete this._volatileSidPull[sid];
		}
	},

	getBySid : function (sid) {
		return this._sidPull[sid];
	},
	getByUid : function (uid) {
		var sid = this._uidPull[uid];
		if (sid) {
			return this.getBySid(sid);
		}
		return null;
	}
};