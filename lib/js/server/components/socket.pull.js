module.exports = {
	_sidPull : {},
	_uidPull : {},
	_volatileSidPull : {},
	_volatileUidPull : {},
	socketNumber : 0,
	add : function (socket) {
		this.socketNumber++;
		var sid = socket.handshake.sid;
		var uid = socket.handshake.uid;
		if (this._volatileSidPull[sid] && !uid) {
			uid = this._volatileSidPull[sid];
		}
		if (!this._sidPull[sid]) {
			this._sidPull[sid] = {};
		}
		this._sidPull[sid][socket.id] = socket;
		if (uid) {
			this.createSidUidLink(sid, uid);
		}
		var self = this;
		socket.on('disconnect', function () {
			self.socketNumber--;
			if (self._sidPull[sid]) {
				if (uid && self._uidPull[uid] && self._uidPull[uid][sid]) {
					//  delete
					delete self._uidPull[uid][sid];
				}
				//  delete socket link
				delete self._sidPull[sid][this.id];
			}
		});
	},
	createSidUidLink : function (sid, uid) {
		if (this._sidPull[sid]) {
            if (!this._uidPull[uid]) {
                this._uidPull[uid] = {};
            }
			this._uidPull[uid][sid] = sid;
			for (var socketId in this._sidPull[sid]) {
				this._sidPull[sid][socketId].handshake.uid = uid;
			}
		}
		this._volatileSidPull[sid] = uid;
        if (!this._volatileUidPull[uid]) {
            this._volatileUidPull[uid] = {};
        }
		this._volatileUidPull[uid][sid] = sid;
	},

	destroySidUidLink : function (sid, uid) {
		if (this._uidPull[uid][sid]) {
			delete this._uidPull[uid][sid];
		}
		if (this._volatileUidPull[uid][sid]) {
			delete this._volatileUidPull[uid][sid];
		}
		if (this._volatileSidPull[sid]) {
			delete this._volatileSidPull[sid];
		}
	},

	getBySid : function (sid) {
		return this._sidPull[sid];
	},
	getByUid : function (uid) {
		var sids = this._uidPull[uid];
		if (sids) {
            var sockets = {};
            for(var sid in sids) {
                var temp = this.getBySid(sid);
                for(var socketId in temp) {
                    sockets[socketId] = temp[socketId];
                }
            }
            return sockets;
        }
		return null;
	}
};
