<?php if (false) require_once __DIR__ . '/../../IDE_Helper.tpl.php';
?>
<?php if($those->app->config->projectConfig->pageStyle == 'backed'):?>
</div>
<?php endif;?>

<div id="jplayer" style="height: 0;"></div>
<div id="m_service" style="display:none;"></div>
<?php foreach($those->app->config->compressorConfig->singlyJs as $jsKey => $jsFile):?>
    <script src="<?=$jsFile?>"></script>
    <script>app.jsLoaded['<?=$jsKey?>'] = true;</script>
<?php endforeach;?>
