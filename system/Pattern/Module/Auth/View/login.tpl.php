<div class="regauth_form_container<?php echo (isset($in_popup)?' popup':'')?>">
	<input class="continue_url" type="hidden" name="continue_url" value="<?php echo $renderer->textForHtml($those->request->getGet('url'))?>">
	<div class="social_login">
		<h3><?php echo $those->lang->get('auth_ui:sign_in_with_social');?></h3>
		<p><?php echo $those->lang->get('auth_ui:sign_in_by_2_clicks');?> <a href="http://ru.wikipedia.org/wiki/OAuth" target="_blank"><?php echo $those->lang->get('auth_ui:how_it_work');?></a></p>
			<?php if($those->auth->config->vkAppId):?>
				<a class="sik-vk big hint hbottom action" data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_vk');?>" href="/auth/vk" target="_blank" data-action="oauthOpen"><span class="ficon-vk"></span></a>
			<?php endif;?>
			<?php if($those->auth->config->instagramAppId):?>
				<a class="sik-ig big hint hbottom action" data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_ig');?>" href="/auth/instagram" target="_blank" data-action="oauthOpen"><span class="ficon-instagram"></span></a>
			<?php endif;?>
			<?php if($those->auth->config->facebookAppId):?>
				<a class="sik-fb big hint hbottom action" data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_fb');?>" href="/auth/facebook" target="_blank" data-action="oauthOpen"><span class="ficon-facebook"></span></a>
			<?php endif;?>
			<?php if($those->auth->config->twitterConsumerKey):?>
				<a class="sik-tw big hint hbottom hlefted action" data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_tw');?>" href="/auth/twitter" target="_blank" data-action="oauthOpen"><span class="ficon-twitter"></span></a>
			<?php endif;?>
			<?php if($those->auth->config->googleAppId):?>
				<a class="sik-gl big hint hbottom hlefted action" data-title="<?php echo $those->lang->get('auth_ui:sign_in_with_gl');?>" href="/auth/google" target="_blank" data-action="oauthOpen"><span class="ficon-gplus"></span></a>
			<?php endif;?>
		<p class="margintop"><?php echo $those->lang->get('auth_ui:or_use_login_and_password');?></p>
	</div>
	<div class="auth_form m_form styled_form">
		<h3><?php echo $those->lang->get('auth_ui:sign_in');?></h3>
		
		<div class="row">
			<span class="label"><?php echo $those->lang->get('auth_ui:sign_in:login_or_email');?>:</span><input class="in" name="logemail" type="text" value="" />
		</div>
		<div class="row">
			<span class="label"><?php echo $those->lang->get('auth_ui:sign_in:password');?>:</span><input class="in enter_submit" name="password" type="password" value="" />
		</div>
		<div class="row in_error"></div>
		<div class="row">
			<span class="label">&nbsp;</span><input type="button" 
			data-action="appSubmit" data-uri="/auth/login_submit"
			class="btn green m_form_submit action" value="<?php echo $those->lang->get('auth_ui:sign_in:submit');?>" />
			<a href="#" data-action="appSubmit" data-uri="/auth/password_recovery_submit"
			   class="whatpass action"><?php echo $those->lang->get('auth_ui:sign_in:forgot_password');?></a>
		</div>
	</div>

	<div class="register_form m_form styled_form">
		<h3><?php echo $those->lang->get('auth_ui:sign_up');?></h3>
		<div class="row">
			<span class="label"><?php echo $those->lang->get('auth_ui:sign_up:login');?>:</span><input class="in" name="login" type="text" value="" />
		</div>
		<div class="row">
			<span class="label"><?php echo $those->lang->get('auth_ui:sign_up:email');?>*:</span><input class="in" name="email" type="email" value="" />
		</div>
		<div class="row">
			<span class="label"><?php echo $those->lang->get('auth_ui:sign_up:password');?>*:</span><input class="in enter_submit" name="password" type="password" value="" />
		</div>
		<div class="row in_error"></div>
		<div class="row">
			<span class="label">&nbsp;</span><input type="button" 
			data-action="appSubmit" data-uri="/auth/register_submit"
			class="btn m_form_submit action" value="<?php echo $those->lang->get('auth_ui:sign_up:submit');?>" />
		</div>
	</div>
</div>