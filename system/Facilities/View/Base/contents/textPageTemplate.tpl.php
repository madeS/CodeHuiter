<?php
if (false) {
	$h1 = '';
	$h2 = '';
	$p = [];
}
?>
<div>
	<?php if(isset($h1) && $h1):?>
		<h1><?=$h1?></h1>
	<?php endif;?>
	<?php if(isset($h2) && $h2):?>
		<h2><?=$h2?></h2>
	<?php endif;?>
	<?php if($p && is_array($p)):?>
		<?php foreach($p as $p_line):?>
			<p><?=$p_line?></p>
		<?php endforeach;?>
	<?php else:?>
		<p><?=$p?></p>
	<?php endif;?>
	
	<h2></h2>
	<p></p>
	<p></p>
</div>