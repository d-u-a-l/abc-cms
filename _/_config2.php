<?php

//database
$config['mysql_server']		= 'localhost';
$config['mysql_username']	= 'root';
$config['mysql_password']	= '';
$config['mysql_database']	= 'abc';
$config['mysql_charset']	= 'UTF8';
$config['mysql_connect']	= false; //по умолчанию база не подключена
$config['mysql_error']		= false; //ошибка подключения к базе

//timezone
$config['timezone']			= 'Europe/Moscow';

//папка со стилями
$config['style'] = 'templates';

//charset
$config['charset']			= 'UTF-8';

error_reporting(E_ALL);
//error_reporting(0);
set_error_handler('error_handler');

date_default_timezone_set($config['timezone']);
ini_set('session.cookie_lifetime', 0);
ini_set('magic_quotes_gpc', 0);

header('Content-type: text/html; charset='.$config['charset']);
header('X-UA-Compatible: IE=edge,chrome=1');

//обработчик ошибок
function error_handler($errno,$errmsg,$file,$line) {
	// Этот код ошибки не включен в error_reporting
	if (!(error_reporting() & $errno)) return;
	//не фиксируем простые ошибки
	if ($errno==E_USER_NOTICE) return true;
	//запись в файл
	$log_file_name = 'error_'.date('Y-m').'.txt';
	$err_str = date('d H:i');
	$err_str.= "\tfile://".$file;
	$err_str.= "\t".$line;
	$err_str.= "\thttp://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$err_str.= "\t".$errmsg;
	$err_str.= "\r\n";
	$fp = fopen($log_file_name, 'a');
	fwrite($fp,$err_str);
	fclose($fp);
	//фатальная ошибка
	if ($errno==E_USER_ERROR) exit(1);
	//не запускаем внутренний обработчик ошибок PHP
	return true;
}

?>