<?php if (false) require_once __DIR__ . '/../../IDE_Helper.tpl.php';

$those->compressor->checkCompress();
?>
<?php if(isset($canonical) && $canonical):?>
    <link rel="canonical" href="<?php echo $canonical ?>" />
<?php elseif(isset($uri) && $uri && $uri != $those->request->uri):?>
    <link rel="canonical" href="<?php echo $those->config['site_url'] . $uri ?>" />
<?php endif;?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $head_title ?></title>
<!-- metas -->
<meta name="description" content="<?php echo (isset($seo) && $seo && $seo['description']) ? $seo['description'] : $head_description ?>"/>
<meta name="keywords" content="<?php echo (isset($seo) && $seo && $seo['keywords']) ? $seo['keywords'] : $head_keywords?>"/>
<meta name="robots" content="<?php echo (isset($head_robots)) ? $head_robots : 'index, follow' ?>"/>
<?php if (isset($head_revisit)):?>
    <meta name="revisit" content="<?php echo $head_revisit ?>">
<?php endif; ?>
<?php if(isset($uri) && $uri):?>
    <meta property="og:url" content="<?php echo $those->config['site_url'] . $uri ?>" />
<?php endif;?>
<meta property="og:title" content="<?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $head_title ?>"/>
<meta property="og:description" content="<?php echo (isset($seo) && $seo && $seo['description']) ? $seo['description'] : $head_description ?>"/>
<meta property="og:image" content="<?php echo $those->config['site_url'] . $head_image?>"/>
<meta name="author" content="<?php echo $those->config['copyright_name'] ?>"/>
<meta name="viewport" content="width=device-width">
<meta name="MobileOptimized" content="320"/>
<meta name="HandheldFriendly" content="true"/>
<!-- icons -->
<link rel="icon" href="/pub/images/favicon.ico" type="image/x-icon"/>
<link rel="shortcut icon" href="/pub/images/favicon.ico" type="image/x-icon"/>
<!-- styles -->
<?php foreach($those->compressor->result['singly']['css'] as $cssfile):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $cssfile?>" />
<?php endforeach;?>
<link type="text/css" rel="stylesheet" href="<?php echo $those->compressor->result['css']?>" />
<!-- scripts -->
<script src="<?php echo $those->compressor->result['js']?>"></script>
