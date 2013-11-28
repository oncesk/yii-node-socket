<?php
/**
 * Created by JetBrains PhpStorm.
 * User: once
 * Date: 11/28/13
 * Time: 11:50 AM
 * To change this template use File | Settings | File Templates.
 *
 * @var NsEventExampleController $this
 *
 */
?>
<ul>
	<li><a href="<?php echo $this->createUrl('nsEventExample/eventListener');?>" target="_blank">Go to this page for catch events example</a></li>
	<li><a href="<?php echo $this->createUrl('nsEventExample/sendEvent');?>">Send simple event</a></li>
	<li><a href="<?php echo $this->createUrl('nsEventExample/sendRoomEvent');?>">Send simple room event</a></li>
</ul>
