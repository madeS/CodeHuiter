<?php


$pages['total_pages'] = (($pages['total'] - 1) / $pages['per_page'] ) + 1;
	$requestUri = \CodeHuiter\Modifier\RequestUriParser::decode($those->request->getUri());
	$requestUriPages = \CodeHuiter\Modifier\RequestUriParser::copy($requestUri);
?>

<div class="pages">
	<?php if($pages['page']-1 > 0):?>
		<?php $requestUriPages['params']['page'] = $pages['page']-1; ?>
		<a href="<?=\CodeHuiter\Modifier\RequestUriParser::encode($requestUriPages)?>" class="bodyajax">&#9668;</a>
	<?php endif;?>
	<?php for($i = 1; $i <= $pages['total_pages']; $i++):?>
		<?php $requestUriPages['params']['page'] = $i; ?>
		<?php if($i < 4):?>
			<a href="<?=\CodeHuiter\Modifier\RequestUriParser::encode($requestUriPages)?>" class="<?=($i==$pages['page'])?' selected':''?> bodyajax"><?=$i?></a>
		<?php elseif(abs($pages['page'] - $i) < 4):?>
			<a href="<?=\CodeHuiter\Modifier\RequestUriParser::encode($requestUriPages)?>" class="<?=($i==$pages['page'])?' selected':''?> bodyajax"><?=$i?></a>
		<?php elseif($i > $pages['total_pages'] - 4):?>
			<a href="<?=\CodeHuiter\Modifier\RequestUriParser::encode($requestUriPages)?>" class="<?=($i==$pages['page'])?' selected':''?> bodyajax"><?=$i?></a>
		<?php endif;?>
	<?php endfor;?>
	<?php if($pages['page']+1 <= $pages['total_pages']):?>
			<?php $requestUriPages['params']['page'] = $pages['page']+1; ?>
		<a href="<?=\CodeHuiter\Modifier\RequestUriParser::encode($requestUriPages)?>" class="bodyajax">&#9658;</a>
	<?php endif;?>
</div>
