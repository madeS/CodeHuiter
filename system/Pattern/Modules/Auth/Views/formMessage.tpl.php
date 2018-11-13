<?php
	$message = $message ?? '';
	$messageType = $messageType ?? '';
?>
<div class="message_holder <?php
	echo ($messageType === 'error') ? ' error' : '';
	echo ($messageType === 'success') ? ' success' : '';
?>">
	<?php echo $message;?>
</div>