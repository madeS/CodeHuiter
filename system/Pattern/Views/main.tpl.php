<?php if (false) require_once __DIR__ . '/IDE_Helper.tpl.php';

?>

<?php if(!$bodyAjax):?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
<head>
	<?php $those->response->render($patternTemplate . 'main_parts/head') ?>
	<?php if(isset($headAfterTpl) && $headAfterTpl):?>
		<?php $those->response->render($template . $headAfterTpl); ?>
	<?php endif;?>
</head>
<body>
<?php $those->response->render($patternTemplate . 'main_parts/body_cont_before') ?>

<div id="body_cont" <?=($userInfo->id)?'data-timezoneoffset="'.$userInfo->timezone.'"':''?>>
<?php else:?>
        <?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $headTitle ?><ajaxbody_separator/>
<?php endif;?>


	<?php if(!isset($customTemplate)):?>
		<?php if (isset($headerTpl)): ?>
			<?php if ($headerTpl): ?>
				<?php $those->response->render($template . $headerTpl); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php $those->response->render($patternTemplate . 'page_parts/header'); ?>
		<?php endif; ?>

		<div id="container" class="<?=($those->app->config->projectConfig->pageStyle === 'backed')?'':'centerwrap'?><?=(isset($wrap_classes))?' '.$wrap_classes:''?>">
	<?php endif;?>

		<?php if(isset($intervalstack) && $intervalstack):?>
			<?php $those->response->render($patternTemplate . 'page_parts/intervalstack'); ?>
		<?php endif;?>
		<?php if(isset($keybinds) && $keybinds):?>
			<?php $those->response->render($patternTemplate . 'page_parts/keybinds'); ?>
		<?php endif;?>
		<?php if(isset($breadcrumbs)):?>
			<?php $those->response->render($patternTemplate . 'page_parts/breadcrumbs'); ?>
		<?php endif;?>

		<?php if (isset($content_data)):?>
			<?php if (is_array($content_data)):?>
				<?php foreach($content_data as $content_data_item):?>
					<?php echo $content_data_item ?>
				<?php endforeach;?>
			<?php else:?>
				<?php echo $content_data ?>
			<?php endif;?>
		<?php endif; ?>

		<?php if (isset($contentTpl)):?>
			<?php if (is_array($contentTpl)):?>
				<?php foreach($contentTpl as $content_tpl_item):?>
					<?php
						if (strpos($content_tpl_item,':') === 0) $content_tpl_item = $template . substr($content_tpl_item,1);
						$those->response->render($content_tpl_item);
					?>
					<?php $those->response->render($template . $content_tpl_item); ?>
				<?php endforeach;?>
			<?php else:?>
				<?php
					if (strpos($contentTpl,':') === 0) $contentTpl = $template . substr($contentTpl,1);
					$those->response->render($contentTpl);
				?>
			<?php endif;?>
		<?php endif; ?>

	<?php if(!isset($customTemplate)):?>
		</div>

		<?php if (isset($footerTpl)): ?>
			<?php if ($footerTpl): ?>
				<?php $those->response->render($template . $footerTpl); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php $those->response->render($patternTemplate . 'page_parts/footer'); ?>
		<?php endif; ?>
	<?php endif;?>

	<div id="pageinfo" class="hidden" data-opened_at="<?php echo $those->date->now ?>"></div>

	<!-- TODO config show only if superuser ??? -->
	<div id="debug_info">

	</div>

<?php if($those->runData['bodyAjax']):?>
<?php else:?>
</div>

	<?php $those->response->render($patternTemplate . 'main_parts/body_cont_after') ?>
	<?php if(isset($bodyAfterTpl) && $bodyAfterTpl):?>
		<?php $those->response->render($template . $bodyAfterTpl); ?>
	<?php endif;?>

	<?php if(($_GET[\CodeHuiter\Core\Benchmark::GET_DEBUG_BENCH_ENABLE] ?? false)):?>
		{#result_time_table}
		{#result_class_table}
	<?php endif;?>

</body>
</html>
<?php endif;?>
