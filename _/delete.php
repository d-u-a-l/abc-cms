<?php
define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'functions/common_func.php');

//удаление старых файлов
$root = ROOT_DIR.'';
$time = 60*60*24; //сутки
if ($handle = opendir($root)) {
	while (false !== ($dir = readdir($handle))) {
		if (strlen($dir)>2 && is_dir($root.$dir)) {
			delete_all($root.$dir,true);
		}
	}
}

?>