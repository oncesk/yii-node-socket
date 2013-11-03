<?php
/**
 * @var SocketTransport $socketTransport
 * @var SocketTransportCommand $this
 */
?>
module.exports = {
	host : '<?php echo $socketTransport->host;?>',
	port : parseInt('<?php echo $socketTransport->port;?>')
};