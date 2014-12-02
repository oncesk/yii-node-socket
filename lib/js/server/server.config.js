module.exports = {
	host : 'yii.handbid.lan',
	port : parseInt('3001'),
	origin : '*:*',
	allowedServers : ["yii.handbid.local","yii.handbid.lan","handbid.local","dev.handbid.com","10.0.0.180","192.168.246.128","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID',
        socketLogFile : '/var/log/yii/node-socket.log'
};
