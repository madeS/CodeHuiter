<div id="content">			
	<div class="delivery">
		<div class="cont">
			<h1 class="dv_head"><?php echo 'Hello world title'?></h1>

            <?php echo 'Hello world'?>
			<br/>
            <?php echo $those->date->fromTime()->forTimezone('UTC')->toFormat('Y-m-d H:i:s',false,true); ?>
			<br/>
            <?php echo $those->date->fromTime()->forUser($those->auth->user)->toFormat('Y-m-d H:i:s',false,true); ?>
		</div>
	</div>
</div>