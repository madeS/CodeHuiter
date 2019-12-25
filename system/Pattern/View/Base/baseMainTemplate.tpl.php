<?php if (false) require_once SYSTEM_PATH . '/Pattern/View/IDE_Helper.tpl.php';
/** @define "$baseTemplatePath" "./" */
$baseTemplatePath = $those->app->config->projectConfig->baseTemplatePath;
?>

<?php if(!$bodyAjax):?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
<head>
	<?php require_once $baseTemplatePath . 'mainParts/headTemplate.tpl.php'; ?>
	<?php if(isset($headAfterTpl) && $headAfterTpl):?>
		<?php $renderer->render($template . $headAfterTpl); ?>
	<?php endif;?>
</head>
<body>
<?php require_once $baseTemplatePath . 'mainParts/bodyContBeforeTemplate.tpl.php'; ?>

<div id="body_cont" <?=($userInfo->exist())?'data-timezoneoffset="'.$userInfo->getTimezone().'"':''?>>
<?php else:?>
        <?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $headTitle ?><ajaxbody_separator/>
<?php endif;?>


	<?php if(!isset($customTemplate)):?>
		<?php if (isset($headerTpl)): ?>
			<?php if ($headerTpl): ?>
				<?php $renderer->render($template . $headerTpl); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php require_once $baseTemplatePath . 'pageParts/headerTemplate.tpl.php'; ?>
		<?php endif; ?>

		<div id="container" class="<?=($those->app->config->projectConfig->pageStyle === 'backed')?'':'centerwrap'?><?=(isset($wrap_classes))?' '.$wrap_classes:''?>">
	<?php endif;?>

		<?php if(isset($intervalstack) && $intervalstack):?>
			<?php require_once $baseTemplatePath . 'pageParts/intervalstackTemplate.tpl.php'; ?>
		<?php endif;?>
		<?php if(isset($keybinds) && $keybinds):?>
			<?php require_once $baseTemplatePath . 'pageParts/keybindsTemplate.tpl.php'; ?>
		<?php endif;?>
		<?php if(isset($breadcrumbs)):?>
			<?php require_once $baseTemplatePath . 'pageParts/breadcrumbsTemplate.tpl.php'; ?>
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
                $renderer->render($content_tpl_item);
					?>
					<?php $renderer->render($template . $content_tpl_item); ?>
				<?php endforeach;?>
			<?php else:?>
				<?php
					if (strpos($contentTpl,':') === 0) $contentTpl = $template . substr($contentTpl,1);
					$renderer->render($contentTpl);
				?>
			<?php endif;?>
		<?php endif; ?>

	<?php if(!isset($customTemplate)):?>
		</div>

		<?php if (isset($footerTpl)): ?>
			<?php if ($footerTpl): ?>
				<?php $renderer->render($template . $footerTpl); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php require_once $baseTemplatePath . 'pageParts/footerTemplate.tpl.php'; ?>
		<?php endif; ?>
	<?php endif;?>

	<div id="pageinfo" class="hidden" data-opened_at="<?php echo $those->date->now ?>"></div>

	<!-- TODO config show only if superuser ??? -->
	<div id="debug_info">
        <?php if(($_GET[\CodeHuiter\Core\CodeLoader::GET_DEBUG_BENCH_ENABLE] ?? false)):?>
			{#result_time_table}
			{#result_class_table}
        <?php endif;?>
	</div>

<?php if($those->runData['bodyAjax']):?>
<?php else:?>
</div>

	<?php require_once $baseTemplatePath . 'mainParts/bodyContAfterTemplate.tpl.php'; ?>

	<div id="jplayer" style="height: 0;"></div>
	<div id="m_service" style="display:none;"></div>
	<?php foreach($those->app->config->compressorConfig->singlyJs as $jsKey => $jsFile):?>
		<script src="<?=$jsFile?>"></script>
		<script>app.jsLoaded['<?=$jsKey?>'] = true;</script>
	<?php endforeach;?>

	<?php if(isset($bodyAfterTpl) && $bodyAfterTpl):?>
		<?php $renderer->render($template . $bodyAfterTpl); ?>
	<?php endif;?>

</body>
</html>
<?php endif;?>
