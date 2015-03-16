<div class="content">
	<h1<?=editable('pages|name|'.$page['id'])?>><?=$page['name']?></h1>
	<?=$html['page_children']?>
	<div<?=editable('pages|text|'.$page['id'],'editable_text')?>><?=$page['text']?></div>
</div>