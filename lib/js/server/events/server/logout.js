var logout = {

	componentManager : null,

	name : 'logout',

	init : function () {},

	handler : function (frame) {
		if (frame.meta.data['uid']) {
			if (this.handshake.session.user.isAuthenticated) {
				this.handshake.session.user.isAuthenticated = false;
				var uid = this.handshake.session.user.id;
				this.handshake.session.user.id = null;
				this.handshake.uid = null;
				this.handshake.writeSession();
				logout.componentManager.get('sp').destroySidUidLink(this.handshake.sid, uid);
			}
		}
	}
};

module.exports = logout;