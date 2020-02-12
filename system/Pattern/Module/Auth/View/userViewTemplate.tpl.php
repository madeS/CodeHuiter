<?php if (false) require_once SYSTEM_PATH . '/Pattern/View/IDE_Helper.tpl.php';
/** @var \CodeHuiter\Pattern\Module\Auth\Model\User $user */
/** @var \CodeHuiter\Pattern\Module\Auth\Model\User $userPhotos */
?>

<div class="page_container">
	<div class="center_container">
		
		<?php if($those->userService->isBanned($userInfo)):?>
			<div class="warning_line">
				<b><?=$those->lang->get('auth_ui:banned.attention');?></b> <?=$those->lang->get('auth_ui:banned.title');?> <br/>
				<?=$those->lang->get(
						'auth_ui:banned.p1',
						[
								'{#a_tag_open}'=>'<a href="'.$those->links->messagesWithUserById($those->app->config->projectConfig->supportUserId).'" class="bodyajax">',
								'{#a_tag_close}'=>'</a>'
						]
				);?>
			</div>
			<p>&nbsp;</p>
		<?php endif;?>
		
		<div class="user_view">
			<div class="status">
				<span class="arr"></span>
				<div class="status_inner">
					<?=$user->getAboutMe()?>
				</div>
			</div>
			
			<div class="clearline"></div>
		</div>	
		
		<?php if(false && $those->userService->isActive($user)):?>
		<?php // TODO user photos here?>
		<?php foreach($profile_photos as $photo):?><img src="<?=$this->mm->store('user_medias',$photo['picture_preview'])?>" alt=""
				 class="action photo_previews<?=($profile_fi['picture_id']==$photo['id'])?' selected':''?>" data-id="<?=$photo['id']?>" 
				 data-medium="<?=$this->mm->store('user_medias',$photo['picture'])?>" 
					data-action="chooseProfilePhoto" 
		/><?php endforeach;?>		
		<?php endif;?>
		

		
	</div>
	<div class="right_container">
		
		<?php $renderer->render($those->app->config->authConfig->viewsPath . 'userSideTemplate.tpl.php')?>
		
	</div>
	<div class="clearline"></div>
</div>

