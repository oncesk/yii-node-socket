(function () {

	var rooms = {};
	var socket;

	/**
	 * Log manager
	 *
	 * @constructor
	 */
	function Logger() {
		this.isDebugMode = true;

		var console = console || {
			info : function () {},
			log : function () {},
			error : function () {}
		};

		if (!console['log']) {
			console.log = function () {}
		}

		if (!console['error']) {
			console.error = function () {}
		}

		if (!console['info']) {
			console.info = function () {}
		}

		this.log = function () {
			if (this.isDebugMode) {
				console.log.apply(console, arguments);
			}
		};

		this.info = function () {
			if (this.isDebugMode) {
				console.info.apply(console, arguments);
			}
		};

		this.error = function () {
			if (this.isDebugMode) {
				console.error.apply(console, arguments);
			}
		};
	}

	var logger = new Logger();

	/**
	 * Hooked event listen manager
	 *
	 * @param scope
	 * @constructor
	 */
	function EventHookManager(scope) {

		var scope = scope || this;

		var events = {
			before : {},
			after : {}
		};

		this.trigger = function (when, event, data) {
			logger.log('Triggered hook manager, when - ' + when + ', event - ' + event);
			if (events[when] && events[when][event]) {
				for (var i in events[when][event]) {
					var executionResult = events[when][event][i].call(scope, data, event);
					if (executionResult === false) {
						return false;
						break;
					}
				}
			}
			return true;
		};

		/**
		 *
		 * @param event
		 * @param fn
		 * @returns {*}
		 */
		this.before = function (event, fn) {
			if (!events.before[event]) {
				events.before[event] = [];
			}
			logger.log('Add new hook: when - before, event - ' + event);
			events.before[event].push(fn);
			return this;
		};

		/**
		 *
		 * @returns {*}
		 */
		this.after = function (event, fn) {
			if (!events.after[event]) {
				events.after[event] = [];
			}
			logger.log('Add new hook: when - after, event - ' + event);
			events.after[event].push(fn);
			return this;
		};
	}

	/**
	 * Event listen manager
	 *
	 * @param scope
	 * @param eventHookListener
	 * @param eventPrefix
	 * @constructor
	 */
	function EventListener(scope, eventHookListener, eventPrefix) {

		var eventPrefix = eventPrefix || '';
		var scope = scope || this;
		var eventHookListener = eventHookListener || new EventHookManager(scope);
		var events = {};

		this.getId = function () {
			if (scope === this) {
				return '';
			}
			return scope.getId();
		};

		this.getHookListener = function () {
			return eventHookListener;
		};

		this.setEventPrefix = function (prefix) {
			eventPrefix = prefix;
		};

		this.before = eventHookListener.before;
		this.after = eventHookListener.after;

		this.emit = function (event, data) {
			logger.log('Received event: ' + event);
			if (eventHookListener.trigger('before', event, data)) {
				if (events[event]) {
					for (var i in events[event]) {
						events[event][i].call(scope, data);
					}
				}
				eventHookListener.trigger('after', event, data);
			}
			return scope;
		};

		var self = this;

		var getInternalEventName = function (event) {
			var id = self.getId();
			if (id == '') {
				return event;
			}
			return id + ':' + event;
		};

		/**
		 *
		 * @param event
		 * @param fn
		 * @returns {*}
		 */
		this.on = function (event, fn) {
			if (!events[event]) {
				events[event] = [];
			}
			events[event].push(fn);
			var self = this;
			socket.on(getInternalEventName(event), function (data) {
				self.emit(event, data);
			});
			return scope;
		};
	}

	/**
	 * Room object needed for joining into some room and catch room events
	 *
	 * @param id
	 * @constructor
	 */
	function Room(id) {

		var eventListener = new EventListener(this);
		eventListener.setEventPrefix('room:');

		this._isJoined = false;
		this._numberOfClients = 0;
		this._error = false;
		this._errorMessage = '';

		this.getId = function () {
			return id;
		};

		this.getMembersCount = function () {
			return this._numberOfClients;
		};

		this.hasError = function () {
			return this._error;
		};

		this.isJoined = function () {
			return this._isJoined;
		};

		this.before = eventListener.before;
		this.after = eventListener.after;
		this.on = eventListener.on;

		/**
		 * Update members count if some socket join to room
		 */
		this.on('system:update.members_count', function (newMembersCount) {
			this._numberOfClients = newMembersCount;
			eventListener.emit('join', newMembersCount)
		});

		this.join = function (fn) {
			if (this.isJoined()) {
				if (fn) {
					fn.call(this, this.isJoined(), this.getMembersCount());
				}
				return;
			}
			var self = this;
			logger.log('Tying to join in room ' + this.getId());
			socket.emit('room_join', this.getId(), function (isJoined, numberOfClientsInRoom) {
				self._isJoined = isJoined;
				if (isJoined) {
					logger.log('Joined in room ' + self.getId());
					logger.log('Room members count ' + numberOfClientsInRoom);
					self._numberOfClients = numberOfClientsInRoom;
				} else {
					self._error = true;
					self._errorMessage = numberOfClientsInRoom;
					logger.log('Room join error: room - ' + self.getId() + ', reason - ' + numberOfClientsInRoom);
				}
				if (fn) {
					fn.call(self, isJoined, numberOfClientsInRoom);
				}
			});
		};
	}

	function YiiNodeSocket() {

		var self = this;

		var eventListener = new EventListener(this);

		this.before = eventListener.before;
		this.after = eventListener.after;
		this.on = eventListener.on;

		socket = io.connect('http://<?php echo $nodeSocket->host;?>:<?php echo $nodeSocket->port;?>/client');

		this.getId = function () {
			return '';
		};

		this.debug = function (flag) {
			logger.isDebugMode = flag;
			return this;
		};

		/**
		 *
		 * @returns {Logger}
		 */
		this.getLogger = function () {
			return logger;
		};

		/**
		 * @param key
		 */
		this.getPublicData = function (key, fn) {
			logger.log('Trying to get public data for key: ' + key);
			socket.emit('public_data', key, function (data) {
				logger.log('Received public data for key: ' + key);
				if (fn) {
					fn(data);
				}
			});
		};

		this.join = function (id, fn) {
			if (id == '') {
				fn(false, 'Room id can not be empty');
				return;
			}
			if (rooms[id]) {
				return rooms[id].join(fn);
			}
			var room = new Room(id);
			room.join(fn);
		};
	}

	window['YiiNodeSocket'] = YiiNodeSocket;
})();