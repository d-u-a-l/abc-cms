<?php
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);
define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'config_db.php');
$query = "SHOW TABLES";
$result = mysql_query($query);
$i=0;
while ($q = mysql_fetch_array($result)) {
	echo $q[0].'<br />';
	mysql_query("REPAIR TABLE  `".$q[0]."`");

}

?>