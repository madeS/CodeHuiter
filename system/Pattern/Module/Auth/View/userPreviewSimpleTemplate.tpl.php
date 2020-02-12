<?php if (false) require_once SYSTEM_PATH . '/Pattern/View/IDE_Helper.tpl.php';
/** @var \CodeHuiter\Pattern\Module\Auth\Model\User $userPreview */
?>
<a href="<?=$those->links->user($userPreview)?>" class="bodyajax user-item">
	<img class="preview" src="<?=$those->content->store('user_medias', $userPreview->getPicturePreview())?>" alt="<?=$those->userService->getPresentName($userPreview)?>"/>
	<?php if($those->userService->isOnline($userPreview)):?>
		<span class="online"></span>s
	<?php endif;?>
	<span class="name tfc"><span class="tfi"><?=$those->userService->getPresentName($userPreview)?></span><span class="tf"></span></span>
</a>