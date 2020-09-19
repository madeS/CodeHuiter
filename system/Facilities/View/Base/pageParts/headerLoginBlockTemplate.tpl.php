<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
?>
<div class="social_login">

<?php if($those->auth->config->vkAppId):?>
	<a class="socbtn sik-vk small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_vk');?>"
	href="<?php echo $those->links->oauth('vk')?>" target="_blank"
	data-action="oauthOpen"><span class="ficon-vk"></span></a>
<?php endif;?>
<?php if($those->auth->config->instagramAppId):?>
	<a class="socbtn sik-ig small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_ig');?>"
	href="<?php echo $those->links->oauth('instagram')?>" target="_blank"
	data-action="oauthOpen"><span class="ficon-instagram"></span></a>
<?php endif;?>
<?php if($those->auth->config->facebookAppId):?>
	<a class="socbtn sik-fb small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_fb');?>"
	href="<?php echo $those->links->oauth('facebook')?>" target="_blank"
	data-action="oauthOpen"><span class="ficon-facebook"></span></a>
<?php endif;?>
<?php if($those->auth->config->twitterConsumerKey):?>
	<a class="socbtn sik-tw small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_tw');?>"
	href="<?php echo $those->links->oauth('twitter')?>" target="_blank"
	data-action="oauthOpen"><span class="ficon-twitter"></span></a>
<?php endif;?>
<?php if($those->auth->config->googleConfig->googleAppId):?>
	<a class="socbtn sik-gl small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_gl');?>"
	href="<?php echo $those->links->oauth('google')?>" target="_blank"
	data-action="oauthOpen"><span class="ficon-gplus"></span></a>
<?php endif;?>
<a href="#/auth" class="btn blue auth action"
	data-action="easyAjax" data-uri="/auth?in_popup=true"><?php echo $those->lang->get('auth_ui:sign_in_btn')?></a>

</div>