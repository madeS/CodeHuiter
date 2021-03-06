<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @define "$baseTemplatePath" "./" */
$baseTemplatePath = $those->app->config->projectConfig->baseTemplatePath;
?>

<?php if($those->app->config->projectConfig->pageStyle === 'backed'):?>
	<div class="centerwrap<?=(isset($wrap_classes))?' '.$wrap_classes:''?>">
	<?/* centerwrap in footer */?>
<?php endif;?>


<?php if($those->app->config->projectConfig->pageStyle === 'backed'):?>

	<div class="header">
		<a class="logo bodyajax noa" href="<?php echo $those->links->main()?>"><?php echo $those->app->config->projectConfig->projectLogo?></a>
		<div class="profile">
			<?php if($userInfo->exist()):?>
				<div class="profile_block" id="profile_panel">
					<?php $this->load->view('mop/header_profile_block.tpl.php')?>
				</div>
			<?php else:?>
				<div class="social_login">
					<?php $this->load->view('mop/header_login_block.tpl.php')?>
				</div>
			<?php endif;?>
			<div class="m_form page_searcher_cont sitesearcher">
				<div class="iblock page_searcher m_form" data-save-params="show," data-uri="/search">
					<input class="page_searcher_input in enter_submit" name="query" type="text" placeholder="Поиск..." value="<?=$this->mm->g($filters['query'])?>">
					<a class="page_searcher_submit m_form_submit btn blue action" data-action="querySearchSubmit"
						href="/search"
						>Найти</a>
				</div>
			</div>
		</div>

		<div class="clearline"></div>
	</div>

	<div class="topmenu">
		<?php
        $topblogs = $this->mblogs->get(array(
            'short_info' => true, 'place' => 'topmenu',
        ));
        ?>
		<?php foreach($topblogs as $topblog):?>
		<?php $bloguri = $this->links->blogPage($topblog);?>
		<a class="bodyajax item transition <?=(isset($uri) && $bloguri == $uri)?' active':''?>" href="<?=$bloguri?>"><?=$topblog['title']?></a>
		<?php endforeach;?>
	</div>



<?php else:?>

	<div class="header <?=(isset($wrap_classes))?' '.$wrap_classes:''?>">
	<div class="wrap">
	<div class="centerwrap<?=(isset($wrap_classes))?' '.$wrap_classes:''?>">

		<a class="logo bodyajax noa" href="<?php echo $those->links->main()?>"><?php echo $those->app->config->projectConfig->projectLogo?></a>

		<div class="mobilemenu" onclick="$(this).parents('.header').toggleClass('menu-active'); return false;"><span class="inner"><span class="ficon-menu"></span></span></div>



		<div class="profile">
			<?php if($userInfo->exist()):?>
				<?php $renderer->render($baseTemplatePath . 'pageParts/headerProfileBlockTemplate')?>
			<?php else:?>
				<?php $renderer->render($baseTemplatePath . 'pageParts/headerLoginBlockTemplate')?>
			<?php endif;?>
		</div>
		<div class="clearline"></div>
	</div>

	<div class="topmenu">
		<div class="centerwrap<?=(isset($wrap_classes))?' '.$wrap_classes:''?>">

<?php /*
        <?php
        $topblogs = $this->mblogs->get(array(
            'short_info' => true, 'place' => 'topmenu',
        ));
        ?>
        <?php foreach($topblogs as $topblog):?>
        <?php $bloguri = $this->links->blogPage($topblog);?>
        <a class="bodyajax item transition <?=(isset($uri) && $bloguri == $uri)?' active':''?>" href="<?=$bloguri?>"><?=$topblog['title']?></a>
        <?php endforeach;?>

        <div class="item" onclick="return false; $(this).find('.subitems').toggle();">
            Что-то список
            <div class="subitems">
                <a class="item bodyajax" href="#">Что-то 1</a>
                <a class="item bodyajax" href="#">Что-то 2</a>
                <a class="item bodyajax" href="#">Что-то 3</a>
            </div>
        </div>

        <a class="item bodyajax" href="<?='http://andrei.bogarevich.com'?>">Контакты</a>

 */ ?>

		</div>
	</div>
	</div>
	</div>

<?php endif;?>

