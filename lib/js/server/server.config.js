module.exports = {
	host : 'yii.handbid.local',
	port : parseInt('61980'),
	origin : '*:*',
	allowedServers : ["yii.handbid.local","dev.handbid.local","handbid.local","dev.handbid.com","10.0.0.180","192.168.246.128","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID',
        socketLogFile : '/var/log/yii/node-socket.log'
};
