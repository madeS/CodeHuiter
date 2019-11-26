<?php if (false) require_once SYSTEM_PATH . '/Pattern/View/IDE_Helper.tpl.php';
?>
<?php if(isset($breadcrumbs) && $breadcrumbs):?>
	<div class="breadcrumbs">
		<a href="/" class="bodyajax crumb first"><span class="ficon-home"> </span></a>
		<?php foreach($breadcrumbs as $crumb):?>
			<?php if(isset($crumb['url'])):?>
				<a href="<?=$crumb['url']?>" class="bodyajax crumb"><?=$crumb['name']?></a>
			<?php else:?>
				<span class="crumb"><?=$crumb['name']?></span>
			<?php endif;?>
		<?php endforeach;?>
	</div>
<?php endif;?>
<?php if(!isset($hide_after_breadcrumbs_line)):?>
<span class="after_breadcrumbs_line"></span>
<?php endif;?>