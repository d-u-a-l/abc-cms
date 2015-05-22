<?php

if (isset($_POST['text'])) {
	$text = stripslashes_smart($_POST['text']);
	$fp = fopen(ROOT_DIR.'sitemap.xml','w');
	$content.= fwrite($fp,$text)>=0 ? '<br />файл обновлен!' : '<br />ошибка записи!';
	fclose($fp);
}

$handle = fopen(ROOT_DIR.'sitemap.xml', "r");
$text = '';
if ($handle) {
	while (($buffer = fgets($handle, 4096)) !== false) $text.= $buffer;
	fclose($handle);
}

$content = '<div style="margin:10px 0 0; padding:5px 10px; font:12px/14px Arial; background:#DFE0E0; border-radius:3px">
	Генерация Sitemap '.(@$config['sitemap_generation']>0 ? '<b style="color:green">включена</b>' : '<b style="color:darkred">выключена</b>').' <a target="_blank" href="/admin.php?m=config#4">настроить</a>
</div>';

$content.= '<form method="post">';
$content.= form('textarea td12','text',$text,array('name'=>' ','attr'=>'style="height:300px"'));
$content.= '<div class="clear"></div><a href="#" class="button red js_submit"><span>Сохранить</span></a>';
$content.= '</form>';
$content.= '<div class="clear"></div>';