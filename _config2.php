<?php

//database
$config['mysql_server']		= 'db4.unlim.com';
$config['mysql_username']	= 'u11788_demo';
$config['mysql_password']	= 'E.(s+y@CkW_l';
$config['mysql_database']	= 'u11788_demo';
//исключение для локальной версии
if ($_SERVER['REMOTE_ADDR']=='127.0.0.1' AND $_SERVER['SERVER_ADDR']=='127.0.0.1') {
	$config['mysql_server'] = 'localhost';
	$config['mysql_username'] = 'root';
	$config['mysql_password'] = '';
	$config['mysql_database'] = 'abc';
}
$config['mysql_charset']	= 'UTF8';
$config['mysql_connect']	= false; //по умолчанию база не подключена
$config['mysql_error']		= false; //ошибка подключения к базе

//массив всех подключаемых css и js файлов
$config['sources'] = array(
	'css_reset'					=> '/templates/css/reset.css',
	'css_common'				=> '/templates/css/common.css?',
	'script_common'				=> '/templates/scripts/common.js?',
	'jquery'					=> '/plugins/jquery/jquery-1.11.1.min.js',
	'jquery_cookie'				=> '/plugins/jquery/jquery.cookie.js',
	'jquery_ui'					=> '/plugins/jquery/jquery-ui-1.11.1.custom.min.js',
	'jquery_ui_style'			=> '/plugins/jquery/redmond/jquery-ui-1.8.17.custom.css',
	'jquery_localization'		=> '/plugins/jquery/i18n/jquery.ui.datepicker-{localization}.js',
	'jquery_form'				=> '/plugins/jquery/jquery.form.min.js',
	'jquery_uploader'			=> '/plugins/jquery/jquery.uploader.js',
	'jquery_validate'			=> array(
		'/plugins/jquery/jquery-validation-1.8.1/jquery.validate.min.js',
		'/plugins/jquery/jquery-validation-1.8.1/additional-methods.min.js',
		'/plugins/jquery/jquery-validation-1.8.1/localization/messages_{localization}.js',
	),
	'jquery_multidatespicker'	=> '/plugins/jquery/jquery-ui.multidatespicker.js',
	'highslide'					=> array(
		'/plugins/highslide/highslide.packed.js',
		'/plugins/highslide/highslide.css',
	),
	'highslide_gallery' 		=> array(
		'/plugins/highslide/highslide-with-gallery.js',
		'/templates/scripts/highslide.js',
		'/plugins/highslide/highslide.css',
	),
	'tinymce'					=> '/plugins/tinymce/tinymce.min.js',
	'editable'					=> '/templates/scripts/editable.js',
);

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