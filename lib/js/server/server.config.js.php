<?php
/**
 * @var NodeSocket $nodeSocket
 * @var NodeSocketCommand $this
 */
?>
module.exports = {
    host : '<?php echo $nodeSocket->host; ?>',
    port : parseInt('<?php echo $nodeSocket->port; ?>'),
    origin : '<?php echo $nodeSocket->getOrigin(); ?>',
    allowedServers : <?php echo json_encode($nodeSocket->getAllowedServersAddresses()); ?>,
    dbOptions : <?php echo json_encode($nodeSocket->getDb()->getConnectionOptions()); ?>,
    checkClientOrigin : <?php echo (int) $nodeSocket->checkClientOrigin; ?>,
    sessionVarName : '<?php echo $nodeSocket->sessionVarName; ?>',
    socketLogFile : '<?php echo $nodeSocket->socketLogFile; ?>'
};

<?php
/*

module.exports = {
	host : 'yii.handbid.lan',
	port : parseInt('3002'),
	origin : '*:*',
	allowedServers : ["yii.handbid.local","yii.handbid.lan","handbid.local","dev.handbid.com","10.0.0.180","192.168.246.128","localhost","127.0.0.1"],
	dbOptions : {"driver":"dummy","config":[]},
	checkClientOrigin : 0,
	sessionVarName : 'PHPSESSID',
        socketLogFile : '/var/log/yii/node-socket.log'
};

*/

