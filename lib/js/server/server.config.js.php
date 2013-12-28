<?php
/**
 * @var NodeSocket $nodeSocket
 * @var NodeSocketCommand $this
 */
?>
module.exports = {
	host : '<?php echo $nodeSocket->host;?>',
	port : parseInt('<?php echo $nodeSocket->port;?>'),
	origin : '<?php echo $nodeSocket->getOrigin(); ?>',
	allowedServers : <?php echo json_encode($nodeSocket->getAllowedServersAddresses()); ?>,
	dbOptions : <?php echo json_encode($nodeSocket->getDb()->getConnectionOptions()); ?>,
	checkClientOrigin : <?php echo (int)$nodeSocket->checkClientOrigin; ?>
};