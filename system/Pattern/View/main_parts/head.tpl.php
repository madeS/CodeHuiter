<?php if (false) require_once __DIR__ . '/../IDE_Helper.tpl.php';

$compressorConfig = $those->compressor->checkCompress();
?>
<?php if(isset($canonical) && $canonical):?>
    <link rel="canonical" href="<?php echo $canonical ?>" />
<?php elseif(isset($uri) && $uri && $uri != $those->request->uri):?>
    <link rel="canonical" href="<?php echo $those->app->config->settingsConfig->siteUrl . $uri ?>" />
<?php endif;?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $headTitle ?></title>
<!-- metas -->
<meta name="description" content="<?php echo (isset($seo) && $seo && $seo['description']) ? $seo['description'] : $headDescription ?>"/>
<meta name="keywords" content="<?php echo (isset($seo) && $seo && $seo['keywords']) ? $seo['keywords'] : $headKeywords ?>"/>
<meta name="robots" content="<?php echo (isset($head_robots)) ? $head_robots : 'index, follow' ?>"/>
<?php if (isset($head_revisit)):?>
    <meta name="revisit" content="<?php echo $head_revisit ?>">
<?php endif; ?>
<?php if(isset($uri) && $uri):?>
    <meta property="og:url" content="<?php echo $those->app->config->settingsConfig->siteUrl . $uri ?>" />
<?php endif;?>
<meta property="og:title" content="<?php echo (isset($seo) && $seo && $seo['title']) ? $seo['title'] : $headTitle ?>"/>
<meta property="og:description" content="<?php echo (isset($seo) && $seo && $seo['description']) ? $seo['description'] : $headDescription ?>"/>
<meta property="og:image" content="<?php echo $those->app->config->settingsConfig->siteUrl . $headImage?>"/>
<meta name="author" content="<?php echo $those->app->config->projectConfig->copyrightName ?>"/>
<meta name="viewport" content="width=device-width">
<meta name="MobileOptimized" content="320"/>
<meta name="HandheldFriendly" content="true"/>
<!-- icons -->
<link rel="icon" href="/pub/images/favicon.ico" type="image/x-icon"/>
<link rel="shortcut icon" href="/pub/images/favicon.ico" type="image/x-icon"/>
<!-- styles -->
<?php foreach($those->app->config->compressorConfig->singlyCss as $cssFile):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $cssFile?>" />
<?php endforeach;?>
<link type="text/css" rel="stylesheet" href="<?php echo $compressorConfig->resultCss?>" />
<!-- scripts -->
<script src="<?php echo $compressorConfig->resultJs?>"></script>
