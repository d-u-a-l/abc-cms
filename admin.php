<?php

// загрузка функций **********************************************************
require_once('functions/global_conf.php');			//глобальные функции
require_once(ROOT_DIR.'functions/config.php');		//общие функции
$config['admin_lang'] = 'ru';
require_once(ROOT_DIR.'admin/languages/'.$config['admin_lang'].'.php');	//язык админки
require_once(ROOT_DIR.'config_db.php');				//доступ к ДБ
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
require_once(ROOT_DIR.'functions/admin_func.php');	//функции для админпанели
require_once(ROOT_DIR.'functions/admin_conf.php');	//настройки админки
require_once(ROOT_DIR.'functions/common_conf.php'); //общие настройки


//аутентификация - создание массива с данными пользователя
session_start();
$user = user('auth'); //print_R($_SESSION['user']);

//объявление переменных *****************************************************
$url = $error = $success = $content = $where = $query = $pattern = $table = '';
$form = $delete = $filter = $template = $tabs = array();
$overlay = true;

// создание get-массива и полного get-запроса ********************************
$get = array('m'=>'','u'=>'','id'=>'','b'=>'','c'=>'','s'=>'','o'=>'');
foreach ($_GET as $k=>$v) {
	$get[$k] = $post[$k] = stripslashes_smart($v);	//создание массива post из get
	$url.= "$k=$v&";			//формировка полного get-запроса
}
if ($get['m']=='') $get['m']='index';

// авторизация ***************************************************************
if (access('admin module',$get['m'])==false) {
	//die(header('location: /admin.php?m=_login'));
	include(ROOT_DIR.'admin/modules/login.php');
	die();
}
// проверка существования модуля *********************************************
if (!file_exists(ROOT_DIR.'admin/modules/'.$get['m'].'.php')) die(header('location: /admin.php?m=index')); //$get[m]='index';

//загрузка основного обработчика действия
if ($get['u'] AND file_exists(ROOT_DIR.'admin/actions/'.$get['u'].'.php')) {	require_once(ROOT_DIR.'admin/actions/'.$get['u'].'.php');
}
//отображать по умолчанию основной шаблон
else {
	if ($get['id']>0) {		//массив данных если есть ИД
		$post = mysql_select("
			SELECT *
			FROM ".$get['m']."
			WHERE id = '".intval($get['id'])."'
		",'row');
	}
	require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
	require_once(ROOT_DIR.'admin/templates/admin_tpl.php');
}

?>