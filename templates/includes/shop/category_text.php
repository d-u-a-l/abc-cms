<div class="shop_category_text">
	<div class="page_text">
		<h1><?=$q['name']?></h1>
		<?php if ($q['text']) {?>
		<?=@$q['text']?>
		<?php } ?>
	<?php if (isset($q['category_list'])) {?>
		<?=$q['category_list']?>
	</div>
	<?php } else {?>
		<?=$q['filter']?>
	</div>
	<div class="page_text"><?=$q['product_list']?><div class="clear"></div></div>
	<?php } ?>
</div>
