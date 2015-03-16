<?

// загрузка функций **********************************************************
require_once('../../functions/global_conf.php');	//общие функции
require_once(ROOT_DIR.'functions/config.php');	//общие функции
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции

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
	if ($order = mysql_select($query,'row')) {		if ($order['paid']==1) {			echo ' PAID';
			$str.= '; PAID';
		}
		else {			mysql_fn('update','orders',array(
				'id'		=> $inv_id,
				'paid'		=> 1,
				'date_paid'	=> $date,
				'payment'	=> 2 //functions/admin_conf.php $config['payments']
			));
			$file = 'success_'.date('Y-m').'.txt';
			echo ' OK';
		}
	}
	else {		echo ' ERROR';
	}
}
$str.= PHP_EOL;

$path = ROOT_DIR.'plugins/robokassa/logs/';
if (is_dir($path) || mkdir ($path,0755,true)) {
	$fp = fopen($path.$file, 'a');
	fwrite($fp,$str);
	fclose($fp);
}

?>


