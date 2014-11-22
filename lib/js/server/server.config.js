module.exports = {
	host : 'dev.handbid.com',
	port : parseInt('61980'),
	origin : '*:*',
	allowedServers : ["dev.handbid.com","dev.handbid.local","handbid.local","192.168.246.128","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID'
};
