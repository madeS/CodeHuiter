<?php if (false) require_once __DIR__ . '/../IDE_Helper.tpl.php';
?>
<div class="profile_block" id="profile_panel">

<input class="notifications_params" type="hidden" name="notifications_count" value="<?php echo $userInfo->notifications_count ?>"/>
<input class="notifications_params" type="hidden" name="notifications_last" value="<?php echo $userInfo->notifications_last ?>"/>
<div class="profile_notifications popshow<?php echo $userInfo->notifications_count ? ' active' : '' ?>">
	<div class="bg action" data-action="showNotifications">
		<span class="cent ficon ficon-bell"></span>
		<span class="cent notifications_count counter"><?php echo $userInfo->notifications_count ?></span>
	</div>
	<div class="pop_cont">
		<div class="popshadow" onclick="$(this).parents('.pop_cont').hide(); return false;"></div>
		<div class="arr"></div>
		<div class="popcontent">
			
		</div>
		<?php /* TODO LOW replace loading animation */ ?>
		<div class="popcontent_loading hidden"><img src="/pub/images/15.gif" alt="loading..." class="imgcentred"/></div>
	</div>
</div>
<div class="profile_menu popshow">
	<div class="bg" onclick="$(this).siblings('.pop_cont').show(); return false;">
		<img src="<?=$those->media->store('user_medias',$userInfo->picture_preview)?>" alt="" />
	</div>
	<div class="pop_cont">
		<div class="popshadow" onclick="$(this).parents('.pop_cont').hide(); return false;"></div>
		<div class="arr"></div>
		<div class="popcontent">
			<div class="popmenu">

				<?php if ($userInfo->isInGroup(\CodeHuiter\Pattern\Modules\Auth\AuthService::GROUP_MODERATOR)): ?>
				<a class="item bodyajax" href="<?=$those->links->blogAdd()?>">Добавить страницу</a>
				<a class="item bodyajax" href="/search">Все страницы</a>
				<?php endif;?>
				<a class="item bodyajax" href="<?=$those->links->user($userInfo)?>">Профиль</a>
				<a class="item bodyajax" href="<?=$those->links->userSettings()?>">Мои настройки</a>
				<a class="item bodyajax" href="<?=$those->links->messages()?>">Мои диалоги</a>
				<a class="item bodyajax" href="/auth/logout">Выйти</a>
			</div>
		</div>
	</div>
</div>

</div>