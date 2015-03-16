<?php

$table = array(
	'id'		=> 'rank:desc id',
	'img'		=> 'img',
	'name'		=> '',
	'url'		=> '',
	'rank'		=> '',
	'display'	=> 'display'
);

$form[] = array('input td8','name',true);
$form[] = array('input td2','rank',true);
$form[] = array('checkbox','display',true);
$form[] = array('input td12','url',true);
$form[] = array('tinymce td12','text',true);

$form[] = array('file td6','img','Основная картинка',array(''=>'resize 1000x1000','p-'=>'cut 750x314'));

?>