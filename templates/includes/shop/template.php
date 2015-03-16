<?php
//страница товара
if ($product) echo $html['content'];
//страница категории
else {	?>
<div class="content">
	<h1<?=$category ? editable('shop_categories|name|'.$page['id']) : editable('pages|name|'.$page['id'])?>><?=$page['name']?></h1>
	<?=$html['category_list']?>
	<?=$html['filter']?>
	<?=$html['product_list']?>
	<div class="clear"></div>
</div>'
	<?php
}
?>