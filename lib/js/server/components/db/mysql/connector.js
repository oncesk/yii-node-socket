var mysql = require('mysql');
function Model(table) {
	this.table = table;
	this.connector = null;
}
Model.prototype.setConnector = function (connector) {
	this.connector = connector;
};
Model.prototype.findByPk = function (pk, fn) {
	var self = this;
	if (typeof pk == 'string' || typeof pk == 'number') {
		pk = [pk];
	}
	this.connector.getConnection(function (connection) {
		var query = "SELECT * FROM `" + self.table + "` WHERE id IN(" + pk.join(',') + ")";
		connection.query(query, fn);
	});
};

Model.prototype.count = function (fn, attributes) {
	var self = this;
	this.connector.getConnection(function (connection) {
		var query = "SELECT COUNT(*) as cnt FROM `" + self.table + "` WHERE 1";
		if (attributes) {
			var toQuery = [];
			for (var key in attributes) {
				toQuery.push(key + " = :" + key);
			}
			query += ' AND ' + toQuery.join(' AND ');
		} else {
			attributes = {};
		}
		connection.query(query, attributes, function (err, result) {
			fn(err, err ? null : result[0]['cnt']);
		});
	});
};

Model.prototype.get = function (fn, attributes, offset, limit) {
	var self = this;
	this.connector.getConnection(function (connection) {
		var where = '';
		if (attributes) {
			var toQuery = [];
			for (var key in attributes) {
				toQuery.push(key + " = :" + key);
			}
			if (toQuery.length > 0) {
				where += ' AND ' + toQuery.join(' AND ');
			}
		} else {
			attributes = {};
		}
		if (!offset && !limit) {
			connection.query("SELECT * FROM `" + self.table + "` WHERE 1 " + where, attributes, fn);
		} else {
			connection.query("SELECT * FROM `" + self.table + "` WHERE 1 "  + where + " LIMIT " + offset + "," + limit, attributes, fn);
		}
	});
};

function Channel() {}
Channel.prototype = new Model('ns_channel');
Channel.prototype._get = Channel.prototype.get;
Channel.prototype.get = function (fn, offset, limit) {
	this._get(function (err, items) {
		if (!err) {
			for (var i in items) {
				if (items[i].properties) {
					items[i].properties = JSON.parse(items[i].properties);
				}
			}
		}
		fn(err, items);
	}, offset, limit);
};

function Subscriber() {}
Subscriber.prototype = new Model('ns_subscriber');

function ChannelSubscriber() {}
ChannelSubscriber.prototype = new Model('ns_channel_subscriber');

var connector = {

	_options : null,
	_connectionOptions : null,
	_connection : null,
	_parseDsn : function (dsn) {
		this._connectionOptions['host'] = dsn.split(';')[0].split('=')[1];
		this._connectionOptions['database'] = dsn.split(';')[1].split('=')[1];
	},

	_createConnection : function (fn) {
		if (!this._connectionOptions) {
			this._connectionOptions = {};
			if (this._options['dsn']) {
				this._parseDsn(this._options['dsn']);
			}
			this._connectionOptions['user'] = this._options['username'];
			this._connectionOptions['password'] = this._options['password'];
		}
		var self = this;
		this._connection = mysql.createConnection(this._connectionOptions);
		this._connection.config.queryFormat = function (query, values) {
			if (!values) return query;
			return query.replace(/\:(\w+)/g, function (txt, key) {
				if (values.hasOwnProperty(key)) {
					return this.escape(values[key]);
				}
				return txt;
			}.bind(this));
		};
		this._connection.connect(function (err) {
			if (err) {
				console.log('error when connecting to db:', err);
			} else {
				if (fn) {
					fn(self._connection);
				}
				self._connection.on('error', function(err) {
					console.log('db error', err);
					if(err.code === 'PROTOCOL_CONNECTION_LOST') { // Connection to the MySQL server is usually
						self._createConnection();                 // lost due to either server restart, or a
					} else {                                      // connnection idle timeout (the wait_timeout
						throw err;                                // server variable configures this)
					}
				});
			}
		});
	},

	init : function (options) {
		this._options = options;
		this.channel = new Channel();
		this.channel.setConnector(this);
		this.subscriber = new Subscriber();
		this.subscriber.setConnector(this);
		this.channelSubscriber = new ChannelSubscriber();
		this.channelSubscriber.setConnector(this);
		return true;
	},

	getConnection : function (fn) {
		if (this._connection) {
			fn(this._connection);
		} else {
			this._createConnection(fn);
		}
	},

	channel : null,
	subscriber : null,
	channelSubscriber : null
};
module.exports = connector;