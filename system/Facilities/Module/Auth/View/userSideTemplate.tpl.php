<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @var \CodeHuiter\Facilities\Module\Auth\Model\User $user */

use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Facilities\Module\Connector\ConnectorService; ?>

<div class="iblock user_view">
	<div class="bheader"><div class="name tfc">
		<span class="tfi">
			<h1><?=$those->userService->getPresentName($user)?></h1>
		</span>
		<span class="tf fcolor"></span>
	</div></div>
	<div class="big_image">
		<img class="pr_img" src="<?=$those->content->store('user_medias', $user->getPicturePreview())?>" alt="<?=$those->userService->getPresentName($user)?>" data-id="<?=$user->getPictureId()?>" />
		<div class="imghider">

			<?php if($those->userService->isOnline($user)):?>
				<div class="onlinestatus online"><span></span><?=$those->lang->get('user:online');?></div>
			<?php else:?>
				<div class="onlinestatus offline">
					<?=$those->lang->get('user:last_visit');?><br/>
					<?=$those->date->fromTime($user->getLastActive())->forUser($userInfo)->toFormat('d.m.y H:i')?>
				</div>
			<?php endif;?>

		</div>
	</div>
	<div class="uinfoline">
	<?php if($those->app->config->projectConfig->usersViewSocialOriginLinks):?>
		<?php if($user->getSocialId('vk')):?>
			<a class="sik-vk small" href="<?='http://vk.com/id'.$user->getSocialId('vk')?>" target="_blank"><span class="ficon-vk"></span></a>
		<?php endif;?>
		<?php if($user->getSocialId('fb')):?>
			<a class="sik-fb small" href="<?='http://facebook.com/'.$user->getSocialId('fb')?>" target="_blank"><span class="ficon-facebook"></span></a>
		<?php endif;?>
		<?php if($user->getSocialId('tw')):?>
			<a class="sik-tw small" href="<?='http://twitter.com/account/redirect_by_id?id='.$user->getSocialId('tw')?>" target="_blank"><span class="ficon-twitter"></span></a>
		<?php endif;?>
		<?php if($user->getSocialId('gl')):?>
			<a class="sik-gl small" href="<?='https://plus.google.com/u/0/'.$user->getSocialId('gl')?>" target="_blank"><span class="ficon-gplus"></span></a>
		<?php endif;?>
	<?php else:?>
		<?php if($user->getSocialId('vk')):?>
			<span class="sik-vk small"><span class="ficon-vk"></span></span>
		<?php endif;?>
		<?php if($user->getSocialId('fb')):?>
			<span class="sik-fb small"><span class="ficon-facebook"></span></span>
		<?php endif;?>
		<?php if($user->getSocialId('tw')):?>
			<span class="sik-tw small"><span class="ficon-twitter"></span></span>
		<?php endif;?>
		<?php if($user->getSocialId('gl')):?>
			<span class="sik-gl small"><span class="ficon-gplus"></span></span>
		<?php endif;?>
	<?php endif;?>
	</div>
	<div class="uinfoline">
		<?php
			$secondline_array = [];
			if ($user->getGender()) {
				$secondline_array[] = ($user->getGender()=== \CodeHuiter\Facilities\Module\Auth\Model\User::GENDER_MALE)
					? $those->lang->get('user:male') : $those->lang->get('user:female');
			}
			$age = $those->userService->getAge($user);
			if ($age) {
				$secondline_array[] =
					StringModifier::fillWordEnd($age, $those->lang->get('user:year_1_2_5'), true)
					.' (' . StringModifier::dateConvert($user->getBirthday(), 'm-ru') . ')';
			}
			if($user->getCity()){
				$secondline_array[] = $user->getCity();
			}
		?>

		<div class="uinfoline">
		<?php if($secondline_array):?>
				<?=implode(', ',$secondline_array)?>
		<?php endif;?>
		</div>
	</div>
	<div class="action_btns">
		<?php if($those->userService->equal($userInfo, $user)):?>
			<span class="btn likea blue action"
				data-action="easyAjax"
				data-uri="/media/popup_photos_upload"
				data-params="<?php echo StringModifier::textForHtml(StringModifier::jsonEncode([
						'object_identity' => ConnectorService::getIdentity($user),
						'as_default' => 1,
				]))?>"
			><span class="ficon-camera"></span> Изменить фото</span>

			<a href="<?=$those->links->userSettings()?>" class="bodyajax btn blue"><span class="ficon-cog"></span> Мои настройки</a>
		<?php endif;?>
		<?php if($those->userService->isActive($user) || $those->userService->isModerator($userInfo)):?>
			<?php if(!$those->userService->equal($userInfo, $user)):?>
				<a href="<?=$those->links->messagesWithUser($user)?>" class="bodyajax btn blue"><span class="ficon-email"></span> <?=$those->lang->get('user:send_message_short');?></a>
			<?php endif;?>
			
		<?php endif;?>
		
		<?php if($those->userService->isModerator($userInfo)):?>
			<?php $json_params = StringModifier::jsonEncode(['user_id'=>$user->getId()]); ?>
			<a href="<?=$those->links->userMedias($user)?>" class="bodyajax btn blue"><span class="ficon-picture"></span> <?=$those->lang->get('user:medias');?></a>
			<a href="<?=$those->links->userAlbums($user)?>" class="bodyajax btn blue"><span class="ficon-folder"></span> <?=$those->lang->get('user:albums');?></a>
			<?php if(!$those->userService->isBanned($user)):?>
				<span class="btn likea blue action" data-action="easyAjax" data-uri="/auth/user_status/banned" data-params="<?=StringModifier::textForHtml($json_params)?>"><span class="ficon-thumbs-down-alt"></span> Заблокировать</span>
			<?php else:?>
				<span class="btn likea blue action" data-action="easyAjax" data-uri="/auth/user_status/active" data-params="<?=StringModifier::textForHtml($json_params)?>"><span class="ficon-thumbs-up-alt"></span> Активировать</span>
			<?php endif;?>
		<?php endif;?>
	</div>
	
</div>