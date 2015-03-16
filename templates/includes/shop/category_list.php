<?php
$img = $q['img'] ? '/files/shop_categories/'.$q['id'].'/img/p-'.$q['img'] : '/styles/'.$config['style'].'/images/no_img.png';
$url = '/'.$modules['shop'].'/'.$q['id'].'-'.$q['url'].'/';
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
?>
<div class="shop_category_list<?=fmod($i,4)==0 ? ' dif' : ''?>">
	<div class="img" ><a href="<?=$url?>" style="background-image:url('<?=$img?>')" title="<?=$title?>"><img src="<?=$img?>" /></a></div>
	<div class="name"><a  href="<?=$url?>" title="<?=$title?>"><?=$q['name']?></a></div>
</div>
<?php if ($num_rows==$i) {?>
<div class="clear"></div>
<?php }?>
