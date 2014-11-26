module.exports = {
	host : 'dev.handbid.com',
	port : parseInt('61980'),
	origin : '*:*',
	allowedServers : ["yii.handbid.local","dev.handbid.local","handbid.local","10.0.0.180","192.168.246.128","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID'
};
