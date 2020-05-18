<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @var array $users */
?>

<div class="page_container">
	<div class="center_container">
		
		<div class="m_form page_searcher_cont">
			<div class="iblock page_searcher m_form" data-save-params="show">
				<input class="page_searcher_input in enter_submit" name="query" type="text" placeholder="Поиск пользователя..." value="<?php echo ($filters['query'] ?? '')?>">
				<span class="page_searcher_submit m_form_submit btn blue action" data-action="querySearchSubmit" 
					>Найти</span>
			</div>
		</div>

		<?php $renderer->render($those->app->config->projectConfig->baseTemplatePath . 'widgets/filterTagsTemplate.tpl.php') ?>

		<div class="iblock">
			<div class="bheader"><h1>Пользователи</h1></div>
			<div class="padded textcentred">
			<?php foreach($users as $user):?>
				<?php $renderer->render($those->app->config->authConfig->viewsPath . 'userPreviewSimpleTemplate.tpl.php', ['userPreview' => $user]);?>
			<?php endforeach;?>
			</div>
			<?php if(isset($pages) && $pages):?>
			<div class="padded">
                <?php $renderer->render($those->app->config->projectConfig->baseTemplatePath . 'widgets/pagesTemplate') ?>
			</div>
			<?php endif;?>
		</div>
		
		
	</div>
	
	<div class="right_container">
		
		<div class="iblock">
			<div class="bheader">Показать</div>
			<div class="child_links items">
				<?php
				$cats = array(
					array('name' => 'Случайные','filter' => array('key' => 'show', 'value' => 'random')),
					array('name' => 'Последние активные','filter' => array('key' => 'show', 'value' => 'lastact')),
					array('name' => 'Онлайн пользователи','filter' => array('key' => 'show', 'value' => 'online')),
				);
				?>
				<?php foreach($cats as $cat):?>
					<?php if(($filters[$cat['filter']['key']] ?? '') == $cat['filter']['value']):?>
						<span class="likea item active"><?=$cat['name']?></span>
					<?php else:?>
						<span class="likea item action" data-action="changeSearchParamSubmit" data-param="<?=$cat['filter']['key']?>:<?=$cat['filter']['value']?>"><?=$cat['name']?></span>
					<?php endif;?>
				<?php endforeach;?>
				
			</div>
		</div>	
		
	</div>
	<div class="clearline"></div>
</div>



