<?php

//die();

//отправка всех писем

require_once(ROOT_DIR.'config_db.php'); //доступ к ДБ
require_once(ROOT_DIR.'functions/common_func.php'); //общие функции

$date =  date('Y-m-d H:i:s');

$where = @$_GET['id']>0 ? " AND id=".intval($_GET['id']):" AND date_sent=0";

$query = "SELECT * FROM letters WHERE 1 $where ORDER BY id LIMIT 50";
$result= mysql_query($query);  echo mysql_error(); //echo $query;
$i=0; $ii=0;
while ($q=mysql_fetch_assoc($result)) {	$ii++;
	if (email($q['sender'],$q['receiver'],$q['subject'],$q['text'])) {		$i++;
		mysql_query("UPDATE letters SET date_sent='".$date."' WHERE id=".$q['id']);
	}
}
mysql_query("DELETE FROM letters WHERE date_sent!=0 AND (date_sent + interval 3 day)<'".$date."' ORDER BY id LIMIT 50");
echo mysql_error();
echo 'Отправлено '.$i.' из '.$ii;
echo '<br />Удалено '.mysql_affected_rows();

?>