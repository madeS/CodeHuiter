<?php

use CodeHuiter\Config\ConnectorConfig;
use CodeHuiter\Pattern\Module\Connector\ConnectableObject;
use CodeHuiter\Pattern\Module\Connector\ConnectorService;

if (false) require_once SYSTEM_PATH . '/Pattern/View/IDE_Helper.tpl.php';
/** @var ConnectableObject $object */
/** @var bool $as_default */
?>

<div class="iblock noshadow">
	<div class="bheader">
		Загрузка фото
		<?php if($object && $object->getConnectorType() === ConnectorConfig::TYPE_ALBUM):?>
			в альбом &quot;<?php echo $object->getConnectorName()?>&quot;
		<?php elseif ($object && $object->getConnectorType() === ConnectorConfig::TYPE_PROFILE):?>
			в фотографии профиля
		<?php else:?>
			неизвестность [!APP-FAILED!]
		<?php endif;?>
	</div>
	<div class="padded">
		<div id="user_medias_upload"></div>
	</div>
	<div class="clearline" style="height: 30px;"></div>
</div>
<script>
    mjsa.mFormUpload('#user_medias_upload', function(response){
        mjsa.popups.closeAll();
        mjsa.bodyUpdate();
    }, {
        url:'/media/photos_upload',
        params: {object_identity: '<?php echo ConnectorService::getIdentity($object)?>', as_default:<?=($as_default)?'1':'0'?>},
        name:'user_media',
        maxSize: 20000000,
        maxFiles: 5,
        oneFileSimple: false,
        multirequests: true,
        multirequestsCallback: function(response){
            if (response === undefined) return false;
            mjsa.html('#m_service',response);
        },

        cancelClass:'btn red'
    });
</script>

