<?php

//чтобы не запрашивали напрямую index.php
if ($_SERVER['REQUEST_URI']=='/index.php') {
	header('HTTP/1.1 301 Moved Permanently');
	die(header('location: http://'.$_SERVER['HTTP_HOST']));
}

session_start();

define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек
// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

//создание двомерного массива $u который передается через реврайтмод
for ($i=0; $i<5; $i++) $u[$i] = isset($_GET['u'][$i]) ? stripslashes_smart($_GET['u'][$i]) : '';

//основной язык
$lang = lang(1);
//lang($u[0],'url'); //для многоязычного сайта

//список модулей на сайте
$modules = mysql_select("SELECT url name,module id FROM pages WHERE module!='pages' AND language=".$lang['id']." AND display=1",'array',60*60);

//аутентификация - создание массива с данными пользователя
$user = user('auth'); //print_r($user);
//принудительная авторизация под админом - для демки
//$_SESSION['user'] = $user = mysql_select("SELECT ut.*,u.*FROM users u LEFT JOIN user_types ut ON u.type = ut.id WHERE u.id=1 LIMIT 1",'row');

//включена заглушка для всех кроме администраторов
if ($config['dummy']==1 AND access('user admin')==false) {
	die(include(ROOT_DIR.'templates/includes/common/dummy.php'));
}

//редиректы
if ($config['redirects']) {
	$request_url = explode('?',$_SERVER['REQUEST_URI']); //print_r($request_url);
	if ($redirect = mysql_select("SELECT * FROM redirects WHERE old_url='".mysql_real_escape_string($request_url[0])."'",'row')) {
		header('HTTP/1.1 301 Moved Permanently');
		header('location: http://'.$_SERVER['SERVER_NAME'].$redirect['new_url']);
		die();
	}
}

$error = 0;
//условие для главной страницы или модуля
$where = ($u[1]=='') ? "module='index'" : "url='".trunslit($u[1])."'";
//sql-запрос в таблицу pages
$query = "
	SELECT *, id AS pid
	FROM pages
	WHERE display=1 AND language=".$lang['id']." AND ".$where ."
	LIMIT 1
"; //echo $query;
//массив $page содержит начальную информацию для страницы, которая может быть изменена/дополнена в модуле
if ($page = mysql_select($query,'row',60*60)) {
	$html['module'] = $page['module'];
	if ($page['level'] > 1) {
		$query = "
			SELECT name,url
			FROM pages
			WHERE left_key <= ".$page['left_key']."
				AND right_key >= ".$page['right_key']."
			ORDER BY left_key DESC
		";
		$breadcrumb['page'] = breadcrumb ($query,'/{url}/',60*60);

	} else $breadcrumb['page'][] = array($page['name'],'/'.$page['url'].'/');
	//загрузка модуля
	if (is_file(ROOT_DIR.'modules/'.$page['module'].'.php')) require_once(ROOT_DIR.'modules/'.$page['module'].'.php');
	else $error++;
}
else $error++;

//404
if ($error>0) {
	header("HTTP/1.0 404 Not Found");
	$page['title'] = $page['name'] = i18n('common|str_no_page_name');
	$html['module'] = 'error';
}
//301 редирект при неккоректном урл
elseif($_SERVER['REQUEST_URI']) {
	$request_url = explode('?',$_SERVER['REQUEST_URI']);
	if (substr($request_url[0], -1)!='/') {
		$url = isset($request_url[1]) ? '?'.$request_url[1] : '';
		header('HTTP/1.1 301 Moved Permanently');
		die(header('location: '.$request_url[0].'/'.$url));
	}
}

//загрузка шаблона
require_once(ROOT_DIR.$config['style'].'/includes/common/template.php');

/*
echo '<!--';
echo defined('config_db') ? 'БД подключена!' : 'Нет подклюения к БД';
print_r($config['queries']);
echo '-->'; /**/

?>