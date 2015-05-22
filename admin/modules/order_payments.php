<?php

$table = array(
	'id'		=> 'rank',
	'name'		=> '',
	'merchant'	=> $config['merchants'],
	'rank'		=> '',
	'display'	=> 'display'
);

$delete = array();

$content = '<div style="margin:10px 0 0; padding:5px 10px; font:12px/14px Arial; background:#DFE0E0; border-radius:3px">
	Платежные агрегаторы <a target="_blank" href="/admin.php?m=config#2">настроить</a>
</div>';

$form[] = array('input td4','name',true);
$form[] = array('select td4','merchant',array(true,$config['merchants']));
$form[] = array('input td1 right','rank',true);
$form[] = array('checkbox','display',true);
$form[] = array('textarea td12','text',true);