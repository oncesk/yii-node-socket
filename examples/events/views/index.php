<?php
/**
 * @var NsEventExampleController $this
 */
?>
<h3>Instruction</h3>
<b>Don't forget run nodejs server <i style="color: #079c59">./yiic node-socket start</i> before example usage</b>
<ul>
	<li>1. Open "Go to this page for catch events example" page for catching events</li>
	<li>2. Open "Send simple event" or "Send simple room event" and see result on "Go to this page for catch events example"</li>
</ul>
<ul>
	<li><a href="<?php echo $this->createUrl('nsEventExample/eventListener');?>" target="_blank">Go to this page for catch events example</a></li>
	<li><a href="<?php echo $this->createUrl('nsEventExample/sendEvent');?>">Send simple event</a></li>
	<li><a href="<?php echo $this->createUrl('nsEventExample/sendRoomEvent');?>">Send simple room event</a></li>
</ul>
