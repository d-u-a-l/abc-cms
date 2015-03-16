<?=html_query('common/slider',"SELECT * FROM slider WHERE display=1 ORDER BY rank DESC",'',60*60)?>
<div class="content">
	<?=i18n('common|txt_index') ? '<div style="padding:0 0 10px">'.i18n('common|txt_index',true).'</div><div class="clear"></div>' : ''?>

	<h2><?=i18n('shop|new',true)?></h2>
	<?=html_query('shop/product_list',"
		SELECT sp.*,sc.url category_url, 1 as h2
		FROM shop_products sp, shop_categories sc
		WHERE sc.display=1 AND sp.category=sc.id AND sp.display = 1
		ORDER BY sp.date DESC
		LIMIT 6
	",'',60*60);?>
	<div class="clear"></div>
	<?=$page['text'] ? '<div style="padding:10px 0"'.editable('pages|text|'.$page['id']).'>'.$page['text'].'</div>' : ''?>
</div>