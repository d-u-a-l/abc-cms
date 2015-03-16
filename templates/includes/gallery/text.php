<?php
if ($q['template']==1) {	?>
	<?=$config['scripts']['highslide_gallery']?>
<ul class="gallery_text">
<?php
$path = 'files/gallery/'.$q['id'].'/images'; //папка от корня основной папки
$root = ROOT_DIR.$path.'/'; //папка от корня сервера
$images = $q['images'] ? unserialize($q['images']) : array();
$i=0;
foreach ($images as $k=>$v) if (@$v['display']==1) {	$i++;
	?>
	<li<?=fmod($i,3)==0 ? ' class="dif"' : ''?>>
		<a onclick="return hs.expand(this, config1 )" href="/<?=$path?>/<?=$k?>/<?=$v['file']?>" title="<?=$v['name']?>"><img src="/<?=$path?>/<?=$k?>/preview<?=$v['file']?>" alt="<?=$v['name']?>" /></a>
		<?=$v['name']?>
	</li>
	<?php
}
?>
</ul>
<div class="clear"></div>
<?php
} else {	echo html_array('gallery/slider',$q);
}
?>
