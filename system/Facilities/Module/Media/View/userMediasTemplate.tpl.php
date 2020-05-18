<?php

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectorService;
use CodeHuiter\Facilities\Module\Media\Model\Media;

if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @var Media[] $userMedias */
/** @var User $user */
?>
<div class="page_container">
	<div class="center_container">

		<div class="m_form page_searcher_cont">
			<div class="iblock page_searcher m_form" data-save-params="show,">
				<input class="page_searcher_input in enter_submit" name="query" type="text" placeholder="Поиск фотографии..." value="<?php echo ($filters['query'] ?? '')?>">
				<span class="page_searcher_submit m_form_submit btn blue action" data-action="querySearchSubmit"
				>Найти</span>
			</div>
		</div>

		<div class="iblock">
			<div class="bheader"><h1>Медиа пользователя</h1></div>
			<div class="control_btns">
				<div class="right_btns">
                    <?php if($those->userService->equal($userInfo, $user)):?>
						<span class="btn blue likea action" data-action="easyAjax" data-uri="/japi_users/popup_videos_upload?object_type_id=profile_0" data-popupname="thepopup_nopadd" data-popupwidth="600">Добавить видео</span>
						<span class="btn blue likea action" data-action="easyAjax" data-uri="/japi_users/popup_photos_upload?object_type_id=profile_0" data-popupname="thepopup_nopadd">Загрузить фото</span>
						<span class="btn blue likea action" data-action="easyAjax" data-uri="/japi_users/popup_zip_upload?object_type_id=profile_0" data-popupname="thepopup_nopadd">Загрузить Архив</span>
                    <?php endif;?>
				</div>
				<div class="left_btns">

				</div>
				<div class="clearline"></div>
			</div>
			<div class="textcentred center_media_previews">
                <?php if(!$userMedias):?>
					<div class="padded centered">
						<?php $those->lang->get('user_medias:no_photos_and_videos')?>
					</div>
                <?php endif;?>
                <?php foreach($userMedias as $userMedia):?>
                    <?php $renderer->render($those->app->config->mediaConfig->viewsPath . 'userMediaPreviewSimpleTemplate', ['mediaPreview' => $userMedia]);?>
                <?php endforeach;?>
			</div>
            <?php if(isset($pages) && $pages):?>
				<div class="padded">
                    <?php $renderer->render($those->app->config->projectConfig->baseTemplatePath . 'widgets/pagesTemplate') ?>
				</div>
            <?php endif;?>
		</div>

	</div>

	<div class="right_container">
        <?php $renderer->render($those->app->config->authConfig->viewsPath . 'userSideTemplate');?>
	</div>
	<div class="clearline"></div>
</div>

