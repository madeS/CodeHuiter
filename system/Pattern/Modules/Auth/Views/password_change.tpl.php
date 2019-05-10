<?php
/** @var \CodeHuiter\Pattern\Modules\Auth\Models\UserInterface $user */
?>
<div class="regauth_form_container<?php echo (isset($in_popup)?' popup':'')?>">

		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('profile:change_password');?></div>
			<div class="padded">
				<div class="m_form styled_form">
					<?php if (isset($email_token) && !$email_token):?>
						<div class="in_error"><?php echo $those->lang->get('auth:incorrect_token')?></div>
					<?php endif;?>

					<input class="in" name="id" type="hidden" value="<?php echo $user->getId()?>" />
					<?php if (isset($email_token)):?>
						<input class="in" name="token" type="hidden" value="<?php echo $email_token?>" />
					<?php else: ?>
						<div class="row">
							<span class="label"><?php echo $those->lang->get('profile:change_password:old_password');?>:</span><input class="in" name="password" type="password" value="" />
						</div>
					<?php endif; ?>
					<div class="row">
						<span class="label"><?php echo $those->lang->get('profile:change_password:new_password');?>:</span><input class="in" name="newpassword" type="password" value="" />
					</div>
					<div class="row">
						<span class="label"><?php echo $those->lang->get('profile:change_password:confirm_new_password');?>:</span><input class="in enter_submit" name="newpassword_conf" type="password" value="" />
					</div>
					<div class="row">
						<span class="label">&nbsp;</span><input type="button"
							data-action="appSubmit" data-uri="/auth/user_edit_password_submit"
							class="btn m_form_submit action" value="<?php echo $those->lang->get('profile:change_password:submit');?>" />
						<?php if(!isset($email_token) && $user->getEmail() && $user->getEmailConfirmed()):?>
							<a href="#" data-action="appSubmit" data-uri="/auth/password_recovery_submit"
							   class="whatpass action"><?php echo $those->lang->get('profile:change_password:forgot_password');?></a>
						<?php endif;?>
					</div>
				</div>

			</div>
		</div>
</div>