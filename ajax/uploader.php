<?php

require_once(ROOT_DIR.'functions/common_func.php');
require_once(ROOT_DIR.'functions/string_func.php');
require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами

//загузка файла во временную директорию
$file = @$_FILES['temp'];
if ($file AND is_array($file)) {
	$file['temp'] = rand(1000000,9999999);
	$file['name'] = strtolower(trunslit($file['name'])); //название файла
	$path = 'files/temp/'.$file['temp']; //папка от корня основной папки
	$root = ROOT_DIR.$path.'/';
	if (is_dir($root) || mkdir ($root,0755,true)) { //создание папок для файла
		copy($file['tmp_name'],$root.$file['name']);
		echo $file['temp'];
	}
}

//удаление старых файлов
$root = ROOT_DIR.'files/temp/';
$time = 60*60*24; //сутки
if ($handle = opendir($root)) {
	while (false !== ($dir = readdir($handle))) {
		if (strlen($dir)>2 && is_dir($root.$dir)) {
			if ((time() - $time) > filemtime($root.$dir)) delete_all($root.$dir,true);
		}
	}
}

?>