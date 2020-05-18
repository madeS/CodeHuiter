<?php

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Facilities\Module\Auth\Model\User;
use CodeHuiter\Facilities\Module\Connector\ConnectableObject;
use CodeHuiter\Facilities\Module\Connector\ConnectorService;
use CodeHuiter\Facilities\Module\Media\Model\Media;
use CodeHuiter\Modifier\StringModifier;

if (false) require_once SYSTEM_PATH . '/Facilities/View/IDE_Helper.tpl.php';
/** @var Media $mediaPreview */
/** @var User $user */
/** @var int $albumId */
/** @var bool $withEditButtons */
?>

<?php
$albumId = $albumId ?? 0;
$withEditButtons = $withEditButtons ?? false;
?>

<div class="media_preview_simple">
	<a href="<?php echo $those->content->store('user_medias', $mediaPreview->getPicture())?>"
        <?php if($mediaPreview->getType() === Media::TYPE_VIDEO):?>
			class="imglinkblock-item action" data-fancybox-group="album<?=$albumId?>" data-action="appPopup"
			data-popupwidth="800" data-popupuri="/japi_users/popup_videos_view?user_media_id=<?=$mediaPreview->getId()?>" data-popupname="thepopup_nopadd"
        <?php elseif($mediaPreview->getType() === Media::TYPE_PHOTO):?>
			class="fancybox imglinkblock-item" data-fancybox-group="album<?=$albumId?>"
        <?php elseif($mediaPreview->getType() === Media::TYPE_ZIP):?>
			class="fancybox imglinkblock-item" data-fancybox-group="album<?=$albumId?>" target="_blank"
        <?php else:?>
			class="fancybox imglinkblock-item" data-fancybox-group="album<?=$albumId?>"
        <?php endif;?>
	>
		<div class="img_cont">
			<div class="img_inside">
				<img class="preview imgcentred transition" src="<?=$those->content->store('user_medias',$mediaPreview->getPicturePreview())?>" alt=""/>
			</div>
		</div>
		<div class="layer transition"></div>
        <?php if($mediaPreview->getType() === Media::TYPE_VIDEO):?>
			<span class="videoplay imgcentred">
			<span class="ficon-play"></span>
		</span>
        <?php endif;?>
		<div class="title_cont">
			<span class="title_inside"><?=$mediaPreview->getTitle()?></span>
		</div>

        <?php if($withEditButtons):?>
            <?php $json_params = StringModifier::jsonEncode(['user_media_id' => $mediaPreview->getId()]); ?>
            <?php if($userInfo->getId() === $mediaPreview->getUserId()):?>
				<div class="btns">
                    <?php if($mediaPreview->getType() === Media::TYPE_PHOTO):?>
						<span class="edit likea action hint hlefted hbottom" data-action="appPopup" data-popupwidth="600" data-popupuri="/japi_users/popup_photos_edit?user_media_id=<?=$mediaPreview->getId()?>" data-title="Редактировать фото" data-popupname="thepopup_nopadd" ><span class="ficon-pencil"></span></span>
						<span class="set_avatar likea action hint hlefted hbottom <?=($userInfo->getPictureId() === $mediaPreview->getId())?' active':''?>" data-action="easyAjax" data-uri="/japi_users/photos_set_avatar" data-params="<?=htmlspecialchars($json_params)?>" data-title="Установить на профиль" ><span class="ficon-address-book"></span></span>
						<span class="crop likea action hint hlefted hbottom" data-action="appPopup" data-popupwidth="500" data-popupuri="/japi_users/popup_crop_user_photo?user_media_id=<?=$mediaPreview->getId()?>" data-title="Редактировать превью" ><span class="ficon-crop"></span></span>
                    <?php endif;?>
                    <?php if($mediaPreview->getType() === Media::TYPE_VIDEO):?>
						<span class="edit likea action hint hlefted hbottom" data-action="appPopup" data-popupwidth="600" data-popupuri="/japi_users/popup_video_edit?user_media_id=<?=$mediaPreview->getId()?>" data-title="Редактировать видео" data-popupname="thepopup_nopadd" ><span class="ficon-pencil"></span></span>
                    <?php endif;?>
					<span class="remove likea action hint hlefted hbottom" data-action="easyAjax" data-confirm="Уверены, что хотите удалить эту фотографию?" data-uri="/japi_users/medias_remove" data-params="<?=htmlspecialchars($json_params)?>" data-title="Удалить" ><span class="ficon-cancel"></span></span>
				</div>
            <?php elseif($those->userService->isModerator($userInfo)):?>
				<div class="btns">
					<span class="remove likea action hint hlefted hbottom" data-action="easyAjax" data-confirm="Уверены, что хотите удалить эту фотографию?" data-uri="/japi_users/medias_remove" data-params="<?=htmlspecialchars($json_params)?>" data-title="Удалить" ><span class="ficon-cancel"></span></span>
				</div>
            <?php endif;?>
        <?php endif;?>
	</a>
</div>

<?php /*
<a href="<?=$this->links->userAlbum($album_preview)?>" class="bodyajax imglinkblock-item">
	<div class="img_cont">
		<div class="img_inside">
			<img class="preview imgcentred transition" src="<?=$this->mm->store('user_medias',$album_preview['picture_preview'])?>" alt="<?=$album_preview['name']?>"/>
		</div>
		<div class="layer transition"></div>
		<div class="title_cont">
			<span class="title_inside"><?=$album_preview['name']?></span>
		</div>

	</div>

</a>
 * */ ?>