<?php if (false) require_once __DIR__ . '/../IDE_Helper.tpl.php';
?>
<?php if(isset($intervalstack) && $intervalstack):?>
	<div id="istack" style="display: none;">
		<?php foreach($intervalstack as $keyMethod => $interval):?>
		<?php if(isset($interval['minlevel']) && $ui['level'] < $interval['minlevel']) continue;?>
		<input type="hidden" class="istack" name="<?=$keyMethod?>" data-timer="<?=$interval['timer']?>" value="<?=$keyMethod?>">
		<?php endforeach;?>
	</div>
<?php endif;?>