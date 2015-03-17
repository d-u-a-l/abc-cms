<?php

/**
 * скрипт проверяет наличие страницы в индексе яндекса
*/

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

if($seo_page = mysql_select("
	SELECT *
	FROM seo_pages
	WHERE display=1
	ORDER BY yandex_check
	LIMIT 1
",'row')) {
	$url = 'http://xmlsearch.yandex.ru/xmlsearch?user='.$config['yandex_user'].'&key='.$config['yandex_key'].'&query='.$_SERVER['SERVER_NAME'].$seo_page['url'];
	if (@$result = file_get_contents($url)) {
		//echo '<textarea name="Name" rows=5 cols=20 wrap="off">'.$result.'</textarea>';
		if (strpos($result, "Искомая комбинация слов нигде не встречается")) $data['yandex_search'] = 0;
		elseif (strpos($result, "results")) $data['yandex_search'] = 1;
		else die('error 2');
		$data['id'] = $v['id'];
		$data['yandex_check'] = date('Y-m-d H:i:s');
		//print_r($data);
		mysql_fn('update',$k,$data);
		echo '1';
	}
	else echo 'error 3';
}
else echo 'error 1';

?>