<?php
$img = $q['img'] ? '/files/shop_products/'.$q['id'].'/img/p-'.$q['img'] : '/'.$config['style'].'/images/no_img.png';
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
$alt = $q['img'] ? 'p-'.$q['img'] : i18n('common|wrd_no_photo');
$url = '/'.$modules['shop'].'/'.$q['category'].'-'.$q['category_url'].'/'.$q['id'].'-'.$q['url'].'/';
?>
<div class="shop_product_random">
	<h2><?=i18n('shop|product_random',true)?></h2>
	<a class="img" href="<?=$url?>" style="background-image:url('<?=$img?>')" title="<?=$title?>"><img src="<?=$img?>" alt="<?=$title?>" /></a>
	<a class="name" href="<?=$url?>" title="<?=$title?>"><?=$q['name']?></a>
	<?php if ($q['price']>0) {?>
	<div class="price">
		<span><?=$q['price']?></span> <?=i18n('shop|currency')?>
		<?php if ($q['price2']>0) {?>
		<s><?=$q['price2']?> <?=i18n('shop|currency')?></s>
		<?php } ?>
	</div>
	<?php } ?>
	<?php if (isset($modules['basket']) AND $q['price']>0) {?>
	<a href="#<?=$q['id']?>" class="js_buy button gray window_open" data-window_id="basket_message" data-price="<?=$q['price']?>" data-id="<?=$q['id']?>"><span><?=i18n('basket|buy')?></span></a>
	<?php } ?>
	<a href="<?=$url?>" class="more button white"><span><?=i18n('common|wrd_more')?></span></a>
</div>
