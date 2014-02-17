<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 11/28/13
 * Time: 11:51 AM
 * To change this template use File | Settings | File Templates.
 */
?>
<div id="state"></div>
<script type="text/javascript">
$(function () {

	var $state = $('#state');
	var updateState = function (message, data) {
		$state.append("<div>" + message + ", data type: [" + typeof data + "]</div>");
	};

	var socket = new YiiNodeSocket();

	socket.on('event.example', function (data) {
		updateState('Fired "event.example" event', data);
	});

	socket.room('example').join(function (success, membersCount) {
		if (success) {
			updateState('Joined into room example: members count [' + membersCount + ']');

			this.on('example.room.event', function (data) {
				updateState('Receive event in example room, event: example.room.event', data);
			});

			this.on('join', function (membersCount) {
				updateState('New member joined, new members count is: ' + membersCount);
			});
		} else {
			updateState('Join error: ' + membersCount);
		}
	});
});
</script>