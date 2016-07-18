var express = require('express');

fs =    require('fs'); 

var options = {
    key:    fs.readFileSync('/var/www/html/hola-trabajo-web/vendor/ratacibernetica/yii2-node-socket/lib/js/server/ssl/server.key'),
    cert:   fs.readFileSync('/var/www/html/hola-trabajo-web/vendor/ratacibernetica/yii2-node-socket/lib/js/server/ssl/server.crt'),
    //ca:     fs.readFileSync('ssl/ca.crt')
};
var app = express();


var server = require('https').createServer(options,app);
var io = require('socket.io').listen(server);
var cookie = require('cookie');
var serverConfiguration = require('./server.config.js');
var storeProvider = express.session.MemoryStore;
var sessionStorage = new storeProvider();

var componentManager = require('./components/component.manager.js');
componentManager.set('config', serverConfiguration);

var eventManager = require('./components/event.manager.js');
var socketPull = require('./components/socket.pull.js');
var db = require('./components/db.js');
db.init(serverConfiguration.dbOptions);

componentManager.set('db', db);
componentManager.set('sp', socketPull);
componentManager.set('io', io);
componentManager.set('eventManager', eventManager);
componentManager.set('sessionStorage', sessionStorage);

server.listen(serverConfiguration.port);//, serverConfiguration.host);
console.log('Listening ' + serverConfiguration.host + ':' + serverConfiguration.port);

//  accept all connections from local server
if (serverConfiguration.checkClientOrigin) {
	console.log('Set origin: ' + serverConfiguration.origin);
	io.set("origins", serverConfiguration.origin);
}

//  client
io.of('/client').authorization(function (handshakeData,accept) {

	if (!handshakeData.headers.cookie) {
		return accept('NO COOKIE TRANSMITTED', false);
	}

	handshakeData.cookie = cookie.parse(handshakeData.headers.cookie);

	var sid = handshakeData.cookie[serverConfiguration.sessionVarName];
	if (!sid) {
		return accept('Have no session id', false);
	}

	handshakeData.sid = sid;
	handshakeData.uid = null;

	//  create write method
	handshakeData.writeSession = function (fn) {
		sessionStorage.set(sid, handshakeData.session, function () {
			if (fn) {
				fn();
			}
		});
	};

	//  trying to get session
	sessionStorage.get(sid, function (err, session) {

		//  create session handler
		var createSession = function () {
			var sessionData = {
				sid : sid,
				cookie : handshakeData.cookie,
				user : {
					role : 'guest',
					id : null,
					isAuthenticated : false
				}
			};

			//  store session in session storage
			sessionStorage.set(sid, sessionData, function () {

				//  authenticate and authorise client
				handshakeData.session = sessionData;
				accept(null, true);
			});
		};

		//  check on errors or empty session
		if (err || !session) {
			if (!session) {

				//  create new session
				createSession();
			} else {

				//  not authorise client if errors occurred
				accept('ERROR: ' + err, false);
			}
		} else {
			if (!session) {
				createSession();
			} else {

				//  authorize client
				handshakeData.session = session;
				handshakeData.uid = session.user.id;
				accept(null, true);
			}
		}
	});

}).on('connection', function (socket) {

	//  add socket to pull
	socketPull.add(socket);

	//  connect socket to him channels
	componentManager.get('channel').attachToChannels(socket);

	//  bind events to socket
	eventManager.client.bind(socket);
});

//  server
io.of('/server').authorization(function (data, accept) {
	if (data && data.address) {
		if (data.headers['cookie']) {
			data.cookie = cookie.parse(data.headers.cookie);
			if (data.cookie.PHPSESSID) {
				data.sid = data.cookie.PHPSESSID;
				var found = false;
				for (var i in serverConfiguration.allowedServers) {
					console.log(i);
					console.log(serverConfiguration);
					if (serverConfiguration.allowedServers[i] == data.address.address) {
						found = true;
						break;
					}
				}
				if (found) {
					var createSession = function () {
						var sessionData = {
							sid : data.cookie.PHPSESSID,
							cookie : data.cookie,
							user : {
								role : 'guest',
								id : null,
								isAuthenticated : false
							}
						};

						//  store session in session storage
						sessionStorage.set(data.cookie.PHPSESSID, sessionData, function () {

							//  authenticate and authorise client
							data.session = sessionData;
							accept(null, true);
						});
					};
					data.writeSession = function (fn) {
						sessionStorage.set(data.cookie.PHPSESSID, data.session, function () {
							if (fn) {
								fn();
							}
						});
					};
					sessionStorage.get(data.cookie.PHPSESSID, function (err, session) {
						if (err || !session) {
							if (!session) {
								createSession();
							} else {
								accept('ERROR: ' + err, false);
							}
						} else {
							if (!session) {
								createSession();
							} else {

								//  authorize client
								data.session = session;
								data.uid = session.user.id;
								accept(null, true);
							}
						}
					});
				} else {
					accept('INVALID SERVER: server host ' + data.address.address + ' not allowed');
					console.log(serverConfiguration.allowedServers);
				}
			} else {
				accept('PHPSESSID is undefined', false);
			}

		} else {
			accept('No cookie', false);
		}
	} else {
		accept('NO ADDRESS TRANSMITTED.', false);
		return false;
	}
}).on('connection', function (socket) {

	//  bind events
	eventManager.server.bind(socket);
});

componentManager.initCompleted();
