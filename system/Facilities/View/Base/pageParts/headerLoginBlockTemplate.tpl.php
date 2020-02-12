<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
?>
<div class="social_login">

<?php if($those->auth->config->vkAppId):?>
	<a class="socbtn sik-vk small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_vk');?>"
	href="/auth/vk" target="_blank"
	data-action="oauthOpen"><span class="ficon-vk"></span></a>
<?php endif;?>
<?php if($those->auth->config->instagramAppId):?>
	<a class="socbtn sik-ig small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_ig');?>"
	href="/auth/instagram" target="_blank"
	data-action="oauthOpen"><span class="ficon-instagram"></span></a>
<?php endif;?>
<?php if($those->auth->config->facebookAppId):?>
	<a class="socbtn sik-fb small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_fb');?>"
	href="/auth/facebook" target="_blank"
	data-action="oauthOpen"><span class="ficon-facebook"></span></a>
<?php endif;?>
<?php if($those->auth->config->twitterConsumerKey):?>
	<a class="socbtn sik-tw small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_tw');?>"
	href="/auth/twitter" target="_blank"
	data-action="oauthOpen"><span class="ficon-twitter"></span></a>
<?php endif;?>
<?php if($those->auth->config->googleAppId):?>
	<a class="socbtn sik-gl small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_gl');?>"
	href="/auth/google" target="_blank"
	data-action="oauthOpen"><span class="ficon-gplus"></span></a>
<?php endif;?>
<a href="#/auth" class="btn blue auth action"
	data-action="easyAjax" data-uri="/auth?in_popup=true"><?php echo $those->lang->get('auth_ui:sign_in_btn')?></a>

</div>