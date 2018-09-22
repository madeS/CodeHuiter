<?php if($those->auth->config['vk_app_id']):?>
	<a class="socbtn sik-vk small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_vk');?>"
	href="/auth/vk" target="_blank"
	data-action="oauthOpen"><span class="ficon-vk"></span></a>
<?php endif;?>
<?php if($those->auth->config['instagram_app_id']):?>
	<a class="socbtn sik-ig small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_ig');?>"
	href="/auth/instagram" target="_blank"
	data-action="oauthOpen"><span class="ficon-instagram"></span></a>
<?php endif;?>
<?php if($those->auth->config['facebook_app_id']):?>
	<a class="socbtn sik-fb small translucent hint hbottom action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_fb');?>"
	href="/auth/facebook" target="_blank"
	data-action="oauthOpen"><span class="ficon-facebook"></span></a>
<?php endif;?>
<?php if($those->auth->config['twitter_consumer_key']):?>
	<a class="socbtn sik-tw small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_tw');?>"
	href="/auth/twitter" target="_blank"
	data-action="oauthOpen"><span class="ficon-twitter"></span></a>
<?php endif;?>
<?php if($those->auth->config['google_app_id']):?>
	<a class="socbtn sik-gl small translucent hint hbottom hlefted action"
	data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_gl');?>"
	href="/auth/google" target="_blank"
	data-action="oauthOpen"><span class="ficon-gplus"></span></a>
<?php endif;?>
<a href="#/auth" class="btn blue auth action"
	data-action="easyAjax" data-uri="/auth?in_popup=true"><?php echo $those->lang->get('auth_ui:sign_in_btn')?></a>