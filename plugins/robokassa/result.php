<?php

// загрузка настроек *********************************************************
define('ROOT_DIR', dirname(__FILE__).'/../../');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
//require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
//require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
//require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
//require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

// регистрационная информация (пароль #2)
// registration info (password #2)
$mrh_pass2 = $config['robokassa_password2'];

// чтение параметров
// read parameters
$out_summ	= @$_REQUEST["OutSum"];
$inv_id		= @$_REQUEST["InvId"];
$shp_item	= @$_REQUEST["Shp_item"];
$crc		= @$_REQUEST["SignatureValue"];

$file = 'error_'.date('Y-m').'.txt';
$date = date('Y-m-d H:i:s');
$str = "$date; id:$inv_id; total:$out_summ;";

$crc = strtoupper($crc);
$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
// проверка корректности подписи
// check signature
if ($my_crc!=$crc) {
	echo 'ERROR';
}
else {
	// признак успешно проведенной операции
	// success
	echo $inv_id;
	// запись в файл информации о проведенной операции
	// save order info to file
	$query = "SELECT * FROM orders WHERE id=".intval($inv_id);
	if ($order = mysql_select($query,'row')) {
		if ($order['paid']==1) {
			echo ' PAID';
			$str.= '; PAID';
		}
		else {
			mysql_fn('update','orders',array(
				'id'		=> $inv_id,
				'paid'		=> 1,
				'date_paid'	=> $date,
				'payment'	=> 2 //admin/config.php $config['payments']
			));
			$file = 'success_'.date('Y-m').'.txt';
			echo ' OK';
		}
	}
	else {
		echo ' ERROR';
	}
}
$str.= PHP_EOL;

//запись лога
$path = ROOT_DIR.'plugins/robokassa/logs/';
if (is_dir($path) || mkdir ($path,0755,true)) {
	$fp = fopen($path.$file, 'a');
	fwrite($fp,$str);
	fclose($fp);
}

?>


