<?php

session_start();

define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек
require_once(ROOT_DIR.'admin/config.php');	//настройки админки
// загрузка функций **********************************************************
require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

$config['admin_lang'] = 'en';
require_once(ROOT_DIR.'admin/languages/'.$config['admin_lang'].'.php');	//язык админки

//основной язык
$lang = lang(1);

//аутентификация - создание массива с данными пользователя
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
if ($get['u'] AND file_exists(ROOT_DIR.'admin/actions/'.$get['u'].'.php')) {
	require_once(ROOT_DIR.'admin/actions/'.$get['u'].'.php');
}
//отображать по умолчанию основной шаблон
else {
	if ($get['id']>0) {
		//массив данных если есть ИД
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