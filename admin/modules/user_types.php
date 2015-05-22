<?php

//исключение при редактировании модуля
if ($get['u']=='edit') {
	$post['access_admin'] = @$post['access_admin'] ? serialize($post['access_admin']) : '';
	$post['access_editable'] = @$post['access_editable'] ? serialize($post['access_editable']) : '';
}

$a18n['ut_name']		= 'название';
$a18n['access_delete']	= 'доступ к удалению';
$a18n['access_ftp']		= 'доступ к ftp';

$table = array(
	'id'			=>	'id',
	'ut_name'		=>	'',
	'access_delete'	=>	'boolean',
	'access_ftp'	=>	'boolean',
);

$delete = array();

foreach ($modules_admin as $key => $value) {
	if (is_array($value)) {
		$list[] = array('id'=>'','name'=>$key,'level'=>1);
		foreach ($value as $k=>$v) $list[]= array('id'=>$v,'name'=>$k,'level'=>2);
	}
	else $list[] = array('id'=>$value,'name'=>$key,'level'=>1);
}
$access_editable_array = array(
	array('id'=>'dictionary','name'=>'Словарь'),
	array('id'=>'pages','name'=>'Страницы'),
	array('id'=>'news','name'=>'Новости'),
	array('id'=>'shop_products','name'=>'Товары'),
	array('id'=>'shop_categories','name'=>'Категории'),
	array('id'=>'shop_brands','name'=>'Производители'),
	array('id'=>'shop_reviews','name'=>'Отзывы'),
	array('id'=>'user_fields','name'=>'Параметры пользователей'),
	array('id'=>'order_deliveries','name'=>'Доставка'),
);

$access_admin = (isset($post['access_admin']) && $post['access_admin']) ? unserialize($post['access_admin']) : array();
$access_editable = (isset($post['access_editable']) && $post['access_editable']) ? unserialize($post['access_editable']) : array();

$form[] = array('multicheckbox td4 f_right tr4','access_admin',array($access_admin,$list),array('name'=>'админпанель'));
$form[] = array('multicheckbox td4 f_right tr4','access_editable',array($access_editable,$access_editable_array),array('name'=>'быстрое редактирование (<a href="/admin.php?m=config">on/off</a>)','style'=>'size="20"'));
$form[] = array('input td4','ut_name',true);
$form[] = array('checkbox td4 line','access_delete',true);
$form[] = array('checkbox td4 line','access_ftp',true);