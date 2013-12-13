var db = {

	cm : null,

	_dbOptions : {},
	_driver : null,

	_createDriver : function () {
		var driver = require('./db/' + this._dbOptions['driver'] + '/connector.js');
		if (driver.init(this._dbOptions)) {
			this._driver = driver;
			return true;
		}
		return false;
	},

	_setUp : function () {
		var self = this;

		var processLimit = 10;

		var loadSubscribers = function (channels) {

		};

		this.getDriver().channel.count(function (err, count) {
			if (!err) {
				var iterations = Math.ceil(count/processLimit);
				var offset = 0;

				var setUp = function () {
					if (iterations == 0) {
						return;
					}
					self.getDriver().channel.get(function (err, data) {
						if (!err) {
							loadSubscribers(data, function () {
								offset += processLimit;
								iterations--;
								process.nextTick(function () {
									setUp();
								});
							});
						}
					}, {}, offset, processLimit);
				};
				setUp();
			}
		});
	},

	onComponentMangerSet : function () {
		var self = this;
		this.cm.on('init.completed', function () {
			self._setUp();
		});
	},

	init : function (dbOptions) {
		this._dbOptions = dbOptions;
	},

	getDriver : function () {
		if (!this._driver) {
			this._createDriver();
		}
		return this._driver;
	}
};

module.exports = db;