<?php if (false) require_once __DIR__ . '/../../IDE_Helper.tpl.php';
?>
<?php if($those->config['pageStyle'] == 'backed'):?>
</div>
<?php endif;?>

<div id="jplayer" style="height: 0;"></div>
<div id="m_service" style="display:none;"></div>
<?php foreach($those->compressor->result['singly']['js'] as $jskey => $jsfile):?>
    <script src="<?=$jsfile?>"></script>
    <script>app.jsLoaded['<?=$jskey?>'] = true;</script>
<?php endforeach;?>
