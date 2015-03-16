<?php

$connect=mysql_connect('localhost','root','') OR die(include(ROOT_DIR.'templates/includes/common/dummy.php'));
mysql_select_db ('abc', $connect) OR die(include(ROOT_DIR.'templates/includes/common/dummy.php'));
mysql_query("SET NAMES 'UTF8'");
mysql_query("SET CHARACTER SET 'UTF8'");
define('config_db', true);

?>