<?php
$img = $q['img'] ? '/files/shop_products/'.$q['id'].'/img/p-'.$q['img'] : '/'.$config['style'].'/images/no_img.png';
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
$alt = $q['img'] ? 'p-'.$q['img'] : i18n('common|wrd_no_photo');
$url = '/'.$modules['shop'].'/'.$q['category'].'-'.$q['category_url'].'/'.$q['id'].'-'.$q['url'].'/';
?>
<div class="shop_product_list<?=fmod($i,3)==0 ? ' dif' : ''?>">
	<a class="img" href="<?=$url?>" style="background-image:url('<?=$img?>')" title="<?=$title?>"><img src="<?=$img?>" alt="<?=$title?>" /></a>
	<a class="name" href="<?=$url?>" title="<?=$title?>"><?=$q['name']?></a>
	<?php if ($q['price']>0) {?>
	<div class="price">
		<span<?=editable('shop_products|price|'.$q['id'])?>><?=$q['price']?></span> <?=i18n('shop|currency',true)?>
		<?php if ($q['price2']>0) {?>
		<s><span<?=editable('shop_products|price2|'.$q['id'])?>><?=$q['price2']?></span> <?=i18n('shop|currency',true)?></s>
		<?php } ?>
	</div>
	<?php } ?>
	<?php if (isset($modules['basket']) AND $q['price']>0) {?>
	<a href="#" class="js_buy button gray window_open" data-window_id="basket_message" data-id="<?=$q['id']?>" data-price="<?=$q['price']?>"><span><?=i18n('basket|buy')?></span></a>
	<?php } ?>
	<a href="<?=$url?>" class="more button white"><span><?=i18n('common|wrd_more')?></span></a>
</div>
