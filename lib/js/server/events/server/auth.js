var auth = {

	componentManager : null,

	name : 'auth',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['uid']) {
			this.handshake.session.user.isAuthenticated = true;
			this.handshake.session.user.id = frame.meta.data['uid'];
			this.handshake.uid = frame.meta.data['uid'];
			this.handshake.writeSession();
			auth.componentManager.get('sp').createSidUidLink(this.handshake.sid, this.handshake.uid);
		}
	}
};

module.exports = auth;