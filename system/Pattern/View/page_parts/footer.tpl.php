<?php if (false) require_once __DIR__ . '/../IDE_Helper.tpl.php';
?>

<div class="footer<?=(isset($wrap_classes))?' '.$wrap_classes:''?>">
	<?php if($those->app->config->projectConfig->pageStyle == 'backed'):?>
	<div class="page_container">
		<div class="center_container">
			<div class="information">
				<div class="line">

				</div>

				<div class="line">
					© <?php echo $those->app->config->projectConfig->projectCompany?>, <?=(date('Y') == $those->app->config->projectConfig->projectYear)?'':$those->app->config->projectConfig->projectYear.' - '?><?=date('Y')?>
				</div>
			</div>
		</div>

		<div class="right_container">
			<div class="support">
				<div class="line">
					<span class="likea action btn" data-action="appPopup" data-popupuri="/auth/feedback"><?=$those->lang->get('feedback:open')?></span>
				</div>
				<div class="line">
					<a href="<?php echo $those->app->config->projectConfig->developingUrl?>" class="dev" target="_blank" title="<?php echo $those->app->config->projectConfig->developingTitle?>">
						<span class="ficon-wrenches"></span>
						Разработка
					</a>
				</div>
			</div>
		</div>
		<div class="clearline"></div>
	</div>
	
	<?php else:?>

	<div class="centerwrap">
		<div class="setlanguage">
			<?php if($those->runData['language'] === 'russian'):?>
				<span>Русский</span>
			<?php else:?>
				<span class="action likea" data-uri="/auth/set_language/russian" data-action="easyAjax">Русский</span>
			<?php endif;?>
			|
			<?php if($those->runData['language'] === 'english'):?>
				<span>English</span>
			<?php else:?>
				<span class="action likea" data-uri="/auth/set_language/english" data-action="easyAjax">English</span>
			<?php endif;?>
		</div>
		<div class="section">
			&copy; <?php echo $those->app->config->projectConfig->projectCompany?>, <?=(date('Y') == $those->app->config->projectConfig->projectYear)?'':$those->app->config->projectConfig->projectYear.' - '?><?=date('Y')?>
		</div>
		<div class="section">
			<span class="likea action" data-action="appPopup" data-popupuri="/auth/feedback">
				<span class="ficon-mail"></span>
				<?=$those->lang->get('feedback:open')?>
			</span>
		</div>
		<div class="section">
			<a href="<?php echo $those->app->config->projectConfig->developingUrl?>" class="dev" target="_blank" title="<?php echo $those->app->config->projectConfig->developingTitle?>">
				<span class="ficon-wrenches"></span>
				Разработка
			</a>
		</div>
	</div>
	
	<?php endif;?>
</div>

<?php if($those->app->config->projectConfig->pageStyle == 'backed'):?>
	</div>  <?/* centerwrap in header */?>
<?php endif;?>