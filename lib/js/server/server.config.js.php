<?php
/**
 * @var NodeSocket $nodeSocket
 * @var NodeSocketCommand $this
 */
?>
module.exports = {
	host : '<?php echo $nodeSocket->host;?>',
	port : parseInt('<?php echo $nodeSocket->port;?>')
};