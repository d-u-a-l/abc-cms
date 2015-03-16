<div class="gallery_list <?=fmod($i,3)==0 ? ' dif' : ''?>">
	<a href="/<?=$modules['gallery']?>/<?=$q['id']?>-<?=$q['url']?>/" title="<?=htmlspecialchars($q['name'])?>"><img src="/files/gallery/<?=$q['id']?>/img/p-<?=$q['img']?>" alt="<?=htmlspecialchars($q['img'])?>"/></a>
	<?=$q['name']?>
</div>