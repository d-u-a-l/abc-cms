<?php

if (isset($_POST['text'])) {
	$text = stripslashes_smart($_POST['text']);
	$fp = fopen(ROOT_DIR.'robots.txt','w');
	$content.= fwrite($fp,$text)>=0 ? '<br />файл обновлен!' : '<br />ошибка записи!';
	fclose($fp);
}

$handle = fopen(ROOT_DIR.'robots.txt', "r");
$text = '';
if ($handle) {
	while (($buffer = fgets($handle, 4096)) !== false) $text.= $buffer;
	fclose($handle);
}

$content.= '<form method="post">';
$content.= form('textarea td12','text',$text,array('name'=>' ','attr'=>'style="height:300px"'));
$content.= '<div class="clear"></div><a href="#" class="button red js_submit"><span>Сохранить</span></a>';
$content.= '</form>';
$content.= '<div class="clear"></div>';