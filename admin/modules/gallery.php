<?php

$template = array(
	1=>'список картинок',
	2=>'листалка',
);

$table = array(
	'id'		=>	'rank name id',
	'img'		=>	'img',
	'name'		=>	'',
	'url'		=>	'',
	'template'	=>	$template,
	'rank'		=>	'',
	'display'	=>	'display',
);

$form[] = array('input td5','name',true);
$form[]	= array('select td3','template',array(true,$template));
$form[] = array('input td1','rank',true);
$form[] = array('checkbox td3','display',true);
$form[] = array('seo','seo url title keywords description',true);
$form[] = array('file','img','основное фото',array(''=>'','p-'=>'cut 213x148'));
$form[] = array('file_multi','images','картинки',array(''=>'resize 700x700','p-'=>'cut 213x148'));