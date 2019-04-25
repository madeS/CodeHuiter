<div class="regauth_form_container<?php use CodeHuiter\Modifiers\StringModifier;

echo (isset($in_popup)?' popup':'')?>">

		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('profile:change_password');?></div>
			<div class="padded">
				<div class="m_form styled_form">
					<div class="row">
						<?php if($those->auth->user->exist()):?>
							<input class="in" name="id" type="hidden" value="<?=$those->auth->user->getId()?>" />
						<?php else:?>
							<?php $those->app->fireException(new \Exception('Change password unknown user identifier'))?>
						<?php endif;?>
						<span class="label"><?php echo $those->lang->get('profile:change_password:old_password');?>:</span><input class="in" name="password" type="password" value="" />
					</div>
					<div class="row">
						<span class="label"><?php echo $those->lang->get('profile:change_password:new_password');?>:</span><input class="in" name="newpassword" type="password" value="" />
					</div>
					<div class="row">
						<span class="label"><?php echo $those->lang->get('profile:change_password:confirm_new_password');?>:</span><input class="in enter_submit" name="newpassword_conf" type="password" value="" />
					</div>
					<div class="row">
						<span class="label">&nbsp;</span><input type="button"
																data-action="appSubmit" data-submituri="/auth/user_edit_password_submit"
																class="btn m_form_submit action" value="<?php echo $those->lang->get('profile:change_password:submit');?>" />
						<?php if($those->auth->user->getEmail() && $those->auth->user->getEmailConfirmed()):?>
							<a href="#" data-action="appSubmit" data-submituri="/auth/password_recovery_submit"
							   class="whatpass action"><?php echo $those->lang->get('profile:change_password:forgot_password');?></a>
						<?php endif;?>
					</div>
				</div>

			</div>
		</div>
</div>