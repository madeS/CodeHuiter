<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
?>
<?php if($those->app->config->projectConfig->pageStyle === 'backed'):?>
<?php $bg_user = $this->mauth->getUserRow($this->mm->app_properties['admin_user_id']); ?>
<div class="backgroundlayer braled" style="background-image: url(<?=$this->mm->store('user_medias',$bg_user['picture_orig'])?>);"><div class="gradi"></div></div>
<div class="backgroundlayercontent">
<?php endif;?>
