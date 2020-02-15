<?php if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @var \CodeHuiter\Facilities\Module\Auth\Model\User $user */
/** @var array $userDataInfoFields */
use CodeHuiter\Modifier\StringModifier;
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

		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('user:settings.title');?></div>
			<div class="padded">
				
		<div class="m_form styled_form">
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get('user:settings.name');?>:
				</span><input class="in" name="name" type="text" value="<?=$user->getName()?>" />
			</label></div>
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get('user:settings.sex');?>:
				</span><select name="gender" class="in">
					<?php if($user->getGender() === 0):?>
					<option value="0">--</option>
					<?php endif;?>
					<option value="1"<?php echo ($user->getGender()===1)?' selected':''?>><?php echo $those->lang->get('user:male');?></option>
					<option value="2"<?php echo ($user->getGender()===2)?' selected':''?>><?php echo $those->lang->get('user:female');?></option>
				</select>
			</label></div>
			<div class="row"><label><?php
					$b_arr = explode('-',$user->getBirthday());
					$b_day = (int)($b_arr[2] ?? 0);
					$b_month = (int)($b_arr[1] ?? 0);
					$b_year = (int)($b_arr[0] ?? 0);
				?>
				<span class="label"><?php echo $those->lang->get('user:settings.birthday');?>:</span><select name="birthday_day" class="in birthday day">
					<?php if(!($b_day > 0)):?>
						<option value="0">--</option>
					<?php endif;?>
					<?php for($i = 1; $i <= 31; $i++):?>
						<option value="<?=$i?>"<?=($b_day===$i)?' selected':''?>><?=$i?></option>
					<?php endfor;?>
				</select><select name="birthday_month" class="in birthday month">
					<?php if(!($b_day > 0)):?>
						<option value="0">--</option>
					<?php endif;?> 
					<?php for($i = 1; $i <= 12; $i++):?>
						<option value="<?=$i?>"<?=($b_month===$i)?' selected':''?>><?=$i?></option>
					<?php endfor;?>
				</select><select name="birthday_year" class="in birthday year">
					<?php if(!($b_year > 0)):?>
						<option value="0">--</option>
					<?php endif;?>
					<?php
							$year_end = (int)date('Y') - 16;
							$year_start = (int)date('Y') - 100;
					?>
					<?php for($i = $year_end; $i > $year_start; $i--):?>
						<option value="<?=$i?>"<?=($b_year===$i)?' selected':''?>><?=$i?></option>
					<?php endfor;?>
				</select>
			</label></div>
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get('user:settings.city');?>:
				</span><input class="in" name="city" type="text" value="<?php echo StringModifier::textForHtml($user->getCity())?>" />
			</label></div>
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get('user:about');?>:
				</span><textarea class="in" name="about_me" ><?php echo StringModifier::replace($user->getAboutMe(),array('<br/>'=>"\n"))?></textarea>
			</label></div>
			
			<?php foreach($userDataInfoFields as $dataInfoFieldKey => $dataInfoField):?>
			<?php if($dataInfoField['type'] === 'string'):?>
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get($dataInfoField['name']);?>:
				</span><input class="in" name="<?=$dataInfoFieldKey?>" type="text" value="<?php
					echo $user->getDataInfo()['info'][$dataInfoFieldKey] ?? ''
				?>" />
			</label></div>
			<?php endif;?>
			<?php endforeach;?>
			
			<div class="row"><label>
				<span class="label">
					<?php echo $those->lang->get('user:settings.timezone');?>
				</span>
				<span class="divinline hint hbottom" data-title="<?php echo $those->lang->get('user:settings.timezone_auto');?>">
					<?php echo $those->lang->get('user:settings.timezone_now');?>:
					<span class="nowtime divinline">
						<?php echo $those->date->fromTime()->forUser($user)->toFormat('H:i:s', false, true);
					?></span>
				</span>
				<script>
					app.checkTimezone();
				</script>
			</label></div>

			<div class="row"><label>
				<?php if($those->app->config->projectConfig->usersViewSocialOriginLinks): ?>
					<span class="label">
						<?php echo $those->lang->get('user:settings.show_my_social_accounts');?>
					</span><input type="checkbox" name="allow_show_social" class="in"
						<?php echo $those->userService->isAllowShowSocial($user) ?'checked="checked"':''?> />
				<?php endif;?>
			</label></div>

			<div class="row">
				<span class="label">&nbsp;</span><input type="button" 
				data-action="appSubmit" data-uri="/users/user_edit_submit"
				class="btn green m_form_submit action" value="<?php echo $those->lang->get('user:settings.submit');?>" />
			</div>
		</div>
				
			</div>
		</div>

		<p>&nbsp;</p>

		<div class="iblock change_logemail">
			<div class="bheader">
				<?php echo $those->lang->get('user:settings.link_or_change_email_or_login');?>
			</div>
			<div class="padded">
				
				<div class="m_form styled_form">
					<div class="row"><label>
						<span class="label">
							<?php echo $those->lang->get('user:settings.link_or_change.login');?>:
						</span><input class="in" name="login" type="text" value="<?=$user->getLogin()?>" />
					</label></div>
					<div class="row"><label>
						<span class="label">
							<?php if($user->getEmail()):?>
							<?php if($user->getEmailConfirmed()):?>
								(<span style="color: green;"><span class="ficon-ok-curved"></span> Подтвержен</span>)
							<?php else:?>
								(<span style="color: red;"><span class="ficon-cancel-curved"></span> Необходимо подтвердить</span>)
							<?php endif;?>
							<?php endif;?>
							<?php echo $those->lang->get('user:settings.link_or_change.email');?>:
						</span><input class="in" name="email" type="email" value="<?php echo $user->getEmail()?>" />
					</label></div>
					<div class="row"><label>
						<span class="label">
							<?php echo $those->lang->get('user:settings.link_or_change.password');?>:
						</span><input class="in enter_submit" name="password" type="password" value="" />
					</label></div>
					<div class="row in_error"></div>
					<div class="row">
						<span class="label">&nbsp;</span><input type="button" 
						data-action="appSubmit" data-uri="/auth/user_edit_logemail_submit"
						class="btn m_form_submit action"
						value="<?php echo $those->lang->get('user:link_or_change.submit');?>" />
					</div>
				</div>
				
			</div>
		</div>
		<p>&nbsp;</p>
		
		<?php if($user->getPassHash()):?>
		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('user:settings.change_password');?>:</div>
			<div class="padded">
				
				<div class="m_form styled_form">
					<div class="row"><label>
						<input class="in" name="logemail" type="hidden" value="<?php echo $user->getEmail()?>" />
						<span class="label">
							<?php echo $those->lang->get('user:settings.change_password.old_password');?>:
						</span><input class="in" name="password" type="password" value="" />
					</label></div>
					<div class="row"><label>
						<span class="label">
							<?php echo $those->lang->get('user:settings.change_password.new_password');?>:
						</span><input class="in" name="newpassword" type="password" value="" />
					</label></div>
					<div class="row"><label>
						<span class="label">
							<?php echo $those->lang->get('user:settings.change_password.confirm_new_password');?>:
						</span><input class="in enter_submit" name="newpassword_conf" type="password" value="" />
					</label></div>
					<div class="row"><label>
						<span class="label">&nbsp;</span><input type="button"
							data-action="appSubmit" data-uri="/auth/user_edit_password_submit"
							class="btn m_form_submit action"
							value="<?php echo $those->lang->get('user:settings.change_password.submit');?>"
						/>
						<?php if($user->getEmail() && $user->getEmailConfirmed()):?>
						<a href="#" data-action="appSubmit" data-uri="/auth/password_recovery_submit"
						   class="whatpass action">
                            <?php echo $those->lang->get('user:settings.change_password.forgot_password');?>
						</a>
						<?php endif;?>
					</label></div>
				</div>
				
			</div>
		</div>
		<p>&nbsp;</p>
		<?php endif;?>
		

		<?php if($those->app->config->authConfig->vkAppId
			|| $those->app->config->authConfig->facebookAppId
			|| $those->app->config->authConfig->twitterConsumerKey
			|| $those->app->config->authConfig->googleConfig->googleAppId
		):?>
		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('user:settings.social_accounts');?></div>
			<div class="control_btns">
				<p><?php echo $those->lang->get('user:settings.help_you_signin_to_this_acc_with_social');?></p>
			</div>
			<div class="padded">
				
				<div class="social_connect">

					<p>
					<?php
						$oauths = $user->getOauthData()
					?>
					<?php if($those->app->config->authConfig->vkAppId):?>
					<?php if($user->getSocialId('vk')):?>
						<a class="sik-vk sik-btn"  href="<?='https://vk.com/id'.$user->getSocialId('vk')?>" target="_blank">
							<span class="ficon-vk"></span>
							<?php if($oauths['vk_id']['profilePhoto'] ?? ''):?>
								<img src="<?=$oauths['vk_id']['profilePhoto']?>" style="width:26px; height: 26px; padding:3px; vertical-align: middle;" alt=""/>
							<?php endif;?>
							<?=$this->mm->g($oauths['vk_id']['name'])?>
						</a>

						<a class="action" href="<?php echo $those->links->oauth('vk')?>" target="_blank" data-action="oauthOpen">
                            <?php echo $those->lang->get('user:settings.link_another_account_vk');?>
						</a>
						или <span class="action likea" data-uri="<?php echo $those->links->oauthUnlink('vk')?>" data-action="easyAjax" data-params="<?php StringModifier::textForHtml('{"sync":"vk"}')?>">Отвязать</span>
					<?php else:?>
						<span class="sik-vk sik-btn"><span class="ficon-vk"></span></span>
						<a class="action" href="<?php echo $those->links->oauth('vk')?>" target="_blank" data-action="oauthOpen">
                            <?php echo $those->lang->get('user:settings.link_account_vk');?>
						</a>
					<?php endif;?>
					<br/>
					<?php endif;?>

					<?php if($those->app->config->authConfig->instagramAppId):?>
						<?php if($user->getSocialId('ig')):?>
							<a class="sik-ig sik-btn"  href="<?='https://instagram.com/'.$user->getSocialId('ig')?>" target="_blank">
								<span class="ficon-instagram"></span>
								<?php if($oauths['ig_id']['profilePhoto'] ?? ''):?>
									<img src="<?=$oauths['ig_id']['profilePhoto']?>" style="width:26px; height: 26px; padding:3px; vertical-align: middle;" alt=""/>
								<?php endif;?>
								<?=$this->mm->g($oauths['ig_id']['name'])?>
							</a>

							<a class="action" href="<?php echo $those->links->oauth('ig')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_another_account_ig');?>
							</a>
							или <span class="action likea" data-uri="<?php echo $those->links->oauthUnlink('ig')?>" data-action="easyAjax" data-params="<?php StringModifier::textForHtml('{"sync":"ig"}')?>">Отвязать</span>
						<?php else:?>
							<span class="sik-ig sik-btn"><span class="ficon-instagram"></span></span>
							<a class="action" href="<?php echo $those->links->oauth('ig')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_account_ig');?>
							</a>
						<?php endif;?>
						<br/>
					<?php endif;?>

					<?php if($those->app->config->authConfig->facebookAppId):?>
						<?php if($user->getSocialId('fb')):?>
							<a class="sik-fb sik-btn"  href="<?='https://facebook.com/'.$user->getSocialId('fb')?>" target="_blank">
								<span class="ficon-facebook"></span>
								<?php if($oauths['fb_id']['profilePhoto'] ?? ''):?>
									<img src="<?=$oauths['fb_id']['profilePhoto']?>" style="width:26px; height: 26px; padding:3px; vertical-align: middle;" alt=""/>
								<?php endif;?>
								<?=$this->mm->g($oauths['fb_id']['name'])?>
							</a>

							<a class="action" href="<?php echo $those->links->oauth('fb')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_another_account_fb');?>
							</a>
							или <span class="action likea" data-uri="<?php echo $those->links->oauthUnlink('fb')?>" data-action="easyAjax" data-params="<?php StringModifier::textForHtml('{"sync":"fb"}')?>">Отвязать</span>
						<?php else:?>
							<span class="sik-fb sik-btn"><span class="ficon-facebook"></span></span>
							<a class="action" href="<?php echo $those->links->oauth('fb')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_account_fb');?>
							</a>
						<?php endif;?>
						<br/>
					<?php endif;?>

					<?php if($those->app->config->authConfig->twitterConsumerKey):?>
						<?php if($user->getSocialId('tw')):?>
							<a class="sik-tw sik-btn"  href="<?='http://twitter.com/account/redirect_by_id?id='.$user->getSocialId('tw')?>" target="_blank">
								<span class="ficon-twitter"></span>
								<?php if($oauths['tw_id']['profilePhoto'] ?? ''):?>
									<img src="<?=$oauths['tw_id']['profilePhoto']?>" style="width:26px; height: 26px; padding:3px; vertical-align: middle;" alt=""/>
								<?php endif;?>
								<?=$this->mm->g($oauths['tw_id']['name'])?>
							</a>

							<a class="action" href="<?php echo $those->links->oauth('tw')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_another_account_tw');?>
							</a>
							или <span class="action likea" data-uri="<?php echo $those->links->oauthUnlink('tw')?>" data-action="easyAjax" data-params="<?php StringModifier::textForHtml('{"sync":"tw"}')?>">Отвязать</span>
						<?php else:?>
							<span class="sik-tw sik-btn"><span class="ficon-twitter"></span></span>
							<a class="action" href="<?php echo $those->links->oauth('tw')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_account_tw');?>
							</a>
						<?php endif;?>
						<br/>
					<?php endif;?>

					<?php if($those->app->config->authConfig->googleConfig->googleAppId):?>
						<?php if($user->getSocialId('gl')):?>
							<a class="sik-gl sik-btn"  href="<?='\'https://plus.google.com/u/0/'.$user->getSocialId('gl')?>" target="_blank">
								<span class="ficon-gplus"></span>
								<?php if($oauths['gl_id']['profilePhoto'] ?? ''):?>
									<img src="<?=$oauths['gl_id']['profilePhoto']?>" style="width:26px; height: 26px; padding:3px; vertical-align: middle;" alt=""/>
								<?php endif;?>
								<?=$this->mm->g($oauths['gl_id']['name'])?>
							</a>

							<a class="action" href="<?php echo $those->links->oauth('gl')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_another_account_gl');?>
							</a>
							или <span class="action likea" data-uri="<?php echo $those->links->oauthUnlink('gl')?>" data-action="easyAjax" data-params="<?php StringModifier::textForHtml('{"sync":"gl"}')?>">Отвязать</span>
						<?php else:?>
							<span class="sik-gl sik-btn"><span class="ficon-gplus"></span></span>
							<a class="action" href="<?php echo $those->links->oauth('gl')?>" target="_blank" data-action="oauthOpen">
								<?php echo $those->lang->get('user:settings.link_account_gl');?>
							</a>
						<?php endif;?>
						<br/>
					<?php endif;?>
					
					</p>
				</div>

			</div>
		</div>
		<p>&nbsp;</p>
		<?php endif;?>
		
		
		<div class="iblock">
			<div class="bheader"><?php echo $those->lang->get('user:settings.remove_account');?></div>
			<div class="control_btns">
				<p><?php echo $those->lang->get('user:settings.this_action_remove_this_account');?></p>
			</div>
			<div class="padded">
				
				<div class="righted">
					<span data-confirm="<?php echo $those->lang->get('user:settings.remove_account.confirm');?>"
						  data-action="easyAjax" data-uri="<?php echo $those->links->deactivateAccount()?>"
						  class="btn action">
						<?php echo $those->lang->get('user:settings.remove_me_from_site');?>
					</span>
				</div>
				
			</div>
		</div>
		<p>&nbsp;</p>
	</div>

	<div class="right_container">
        <?php $renderer->render($those->app->config->authConfig->viewsPath . 'userSideTemplate.tpl.php')?>
	</div>
	<div class="clearline"></div>
</div>


