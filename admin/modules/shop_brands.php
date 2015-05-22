<?php

$table = array(
	'id'		=>	'name:asc id',
	'name'		=>	'',
	'url'		=>	'',
	'rank'		=>	'',
	'display'	=>	'display'
);

$delete['confirm'] = array('shop_products'=>'brand');

$form[] = array('input td8','name',true);
$form[] = array('input td2','rank',true);
$form[] = array('checkbox','display',true);
$form[] = array('tinymce td12','text',true);

$form[] = array('seo','seo url title keywords description',true);