<?php

$table = array(
	'id'		=>	'rank',
	'name'		=>	'',
	'rank'		=>	'',
	'cost'		=>	'right',
	'free'		=>	'right',
	'display'	=>	'display'
);

$delete = array();

$form[] = array('input td5','name',true);
$form[] = array('input td1 right','rank',true);
$form[] = array('input td2 right','cost',true,array('name'=>'стоимость доставки'));
$form[] = array('input td2 right','free',true,array('help'=>'укажите минимальную стоимость заказа для которого будет доставка бесплатной'));
$form[] = array('checkbox','display',true);
$form[] = array('textarea td12','text',true);