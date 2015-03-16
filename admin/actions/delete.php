<?php

if (access('admin delete')==false) die('у вас нет доступа к удалению!');

$message = '';
//типы удаления
$array = array(
	'file',	//удаление файла из подпапки (загрузка при помощи simple)
	'key',	//удаление ключа и файла  (загрузка при помощи mysql и file)
	'id'	//удаление записи и всех файлов
);

$type	= isset($_GET['type']) ? $_GET['type'] : '';			//вид удаления
$module = isset($_GET['m']) ? $_GET['m'] : '';					//модуль или папка
$id		= isset($_GET['id']) ? abs(intval($_GET['id'])) : 0;	//индекс
$key 	= isset($_GET['key']) ? $_GET['key'] : '';				//ключ в БД
$file	= isset($_GET['file']) ? $_GET['file'] : '';			//имя файла

if (!in_array($type,$array)) 			die ('ошибка типа удаления!');
if ($id==0)								die ('ошибка индекса!');
if (!preg_match("/^[a-z_]+$/",$module))	die ('ошибка модуля!');
if (!preg_match("/^[a-z0-9_]*$/",$key))	die ('ошибка ключа!');
//if (!preg_match("/^[a-z0-9_.]*$/",$file))	die ('ошибка имени файла!');

if (file_exists(ROOT_DIR.'admin/modules/'.$module.'.php')) {
	require_once(ROOT_DIR.'admin/modules/'.$module.'.php');
}
//удаление файла из подпапки (загрузка при помощи simple)
if ($type=='file') {	$dir	= "files/$module/$id/$key/";	$path	= ROOT_DIR.$dir.$file;
	if (is_file($path)) {
		if (unlink($path)) {			$message = '1';
			if (is_dir(ROOT_DIR.$dir) && $handle = opendir(ROOT_DIR.$dir)) {
				while (false !== ($folder = readdir($handle))) {
					if ($folder!='.' && $folder!='..') {						if (is_dir(ROOT_DIR.$dir.$folder))
							if (is_file(ROOT_DIR.$dir.$folder.'/'.$file))
								unlink(ROOT_DIR.$dir.$folder.'/'.$file);
					}
				}
				closedir($handle);
			}
		}
		else $message = "не удалось удалить файл!";
	}
	else $message = 1;//"нет такого файла!"."files/$module/$id/$key/$file";

//удаление ключа и файла  (загрузка при помощи mysql и file)
} elseif ($type=='key') {
	$path = ROOT_DIR."files/$module/$id/$key/";
	if (is_dir($path)) {
		delete_all($path);
		if (is_dir($path)) $message = 'файл удален, но запись о файле осталась!';
		else $message = "не удалось удалить файл!";
	}
	if (!is_dir($path)) {
		mysql_query("UPDATE `$module` SET `$key` = '' WHERE id = $id");
		if (mysql_affected_rows()!=1) $message = 'не удалось удалить файл';
		else $message = '1';
	}

//удаление записи и всех файлов
} elseif ($type=='id') {
	if (is_array($delete)) {
		if ($content = html_delete($delete)) die(strip_tags($content));
	}
	if (strlen($module)<3 OR $id==0) die ('удаление невозможно!');
	$result = mysql_query("SELECT * FROM `$module` WHERE id = '".$id."'");
	$q = mysql_fetch_assoc($result);
	if (mysql_num_rows($result)==0) die('нет такой записи');
	mysql_query("DELETE FROM `$module` WHERE `id` = $id LIMIT 1");
	//nested sets - пересортировка
	if (array_key_exists('level',$q)) {		$where = '';
		if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
			$where.= " AND `".$v[0]."` = ".intval($q[$v[0]]);
		}
	 	mysql_query("
	 		UPDATE `$module`
			SET left_key = CASE WHEN left_key > ".$q['left_key']."
								THEN left_key - 2
								ELSE left_key END,
				right_key = right_key-2
			WHERE right_key > ".$q['right_key']." AND level>0".$where
		);
	}
	//depend - удаление связей
	if (isset($config['depend'][$module])) {
		foreach ($config['depend'][$module] as $k=>$v) {
			$query = "DELETE FROM `$v` WHERE child = '".intval($id)."'";
			$result = mysql_query($query); echo mysql_error(); //echo $query;
		}
	}
	//проверка удаления
	$result = mysql_query("SELECT * FROM `$module` WHERE `id` = $id LIMIT 1");
	if (mysql_num_rows($result)<1) {		//$message = 'запись удалена!';		delete_all(ROOT_DIR."files/$module/$id");
		if (is_dir(ROOT_DIR."files/$module/$id")) $message = 'не удалось удалить папку';//$message = "все картинки, связанные с записью, удалены! [files/$module/$id]";
		//удаление из связных таблиц
		if (isset($delete['delete'])) {			if (is_array($delete['delete'])) foreach ($delete['delete'] as $k=>$v) mysql_query($v);
			else mysql_query($delete['delete']);
		}
		//логирование
		$logs = array(
			'user'		=> $user['id'],
			'date'		=> date('Y-m-d H:i:s'),
			'parent'	=> $id,
			'module'	=> $module,
			'type'		=> 3
		);
		mysql_fn('insert','logs',$logs);
		$message = 1;
	}
	else $message = 'удаление невозможно!';

} else $message = 'ошибка!';

echo $message;
die();

?>