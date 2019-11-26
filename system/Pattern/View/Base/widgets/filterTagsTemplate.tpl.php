<?php // IN $widget_user 

// CONTROLLER

$filterTags = array();
if ($filters['country'] ?? ''){
	$filterTagCountry = $this->user_geo->countriesGet(array('id' => $filters['country']));
	$filterTags[] = array('param' => 'country:','name' => 'Страна: ' . ($filterTagCountry['name_ru'] ?? ''),);
}
if ($filters['city'] ?? ''){
	$filterTagCountry = $this->user_geo->citiesGet(array('id' => $filters['city']));
	$filterTags[] = array('param' => 'city:','name' => 'Город: ' . ($filterTagCountry['name_ru'] ?? ''),);
}


// VIEW
?>
<?php if($filterTags):?>
<div class="filter_tags">
	<?php foreach($filterTags as $filterTag):?>
	<div class="item">
		<?=$filterTag['name']?>
		<span class="likea action" data-action="changeSearchParamSubmit" data-param="<?=$filterTag['param']?>"><span class="ficon-cancel"></span></span>
	</div>
	<?php endforeach;?>
</div>
<?php endif;?>