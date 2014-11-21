module.exports = {
	host : 'dev.handbid.com',
	port : parseInt('61980'),
	origin : '*:*',
	allowedServers : ["0.0.0.0","dev.handbid.com","dev.handbid.local","handbid.local","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID'
};
