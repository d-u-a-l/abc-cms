<div class="content">
	<h1<?=$news ? editable('news|name|'.$page['id']) : editable('pages|name|'.$page['id'])?>><?=$page['name']?></h1>
	<?=$html['content']?>
	<div class="clear"></div>
</div>