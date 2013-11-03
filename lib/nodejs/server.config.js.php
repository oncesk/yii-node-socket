<?php
/**
 * @var SocketTransport $socketTransport
 * @var SocketTransportCommand $this
 */
?>
module.exports = {
	host : '<?php echo $socketTransport->host;?>',
	port : parseInt('<?php echo $socketTransport->port;?>'),
	allowClientSubscribe : <?php echo (int) $socketTransport->allowClientSubscribe;?>,
	allowClientUnSubscribe : <?php echo (int) $socketTransport->allowClientUnSubscribe;?>,
	serverNamespace : '<?php echo $socketTransport->serverNamespace;?>',
	clientNamespace : '<?php echo $socketTransport->clientNamespace;?>'
};