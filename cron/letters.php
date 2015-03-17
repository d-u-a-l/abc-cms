<?php

/**
 * отправка писем из БД
 */

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
//require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
//require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
//require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
//require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
//require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

$date =  date('Y-m-d H:i:s');

$where = @$_GET['id']>0 ? " AND id=".intval($_GET['id']):" AND date_sent=0";

$query = "SELECT * FROM letters WHERE 1 $where ORDER BY id LIMIT 50";
$result= mysql_query($query);  echo mysql_error(); //echo $query;
$i=0; $ii=0;
while ($q=mysql_fetch_assoc($result)) {
	$ii++;
	if (email($q['sender'],$q['receiver'],$q['subject'],$q['text'])) {
		$i++;
		mysql_query("UPDATE letters SET date_sent='".$date."' WHERE id=".$q['id']);
	}
}
mysql_query("DELETE FROM letters WHERE date_sent!=0 AND (date_sent + interval 3 day)<'".$date."' ORDER BY id LIMIT 50");
echo mysql_error();
echo 'Отправлено '.$i.' из '.$ii;
echo '<br />Удалено '.mysql_affected_rows();

?>