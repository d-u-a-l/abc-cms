<?php

session_start();

//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

//определение значений формы
$fields = array(
	'product'		=> 'required int',
	'rating'		=> 'int',
	'email'			=> 'required email',
	'name'			=> 'required text',
	'text'			=> 'required text',
	'captcha'		=> 'required captcha2'
);
//создание массива $post
$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);

//сообщения с ошибкой заполнения
$message = form_validate($fields,$post);

//если нет ошибок то отправляем сообщение
if (count($message)==0) {
	if ($product = mysql_select("SELECT sp.*,sc.url category_url FROM shop_products sp, shop_categories sc WHERE sp.category=sc.id AND sp.id=".$post['product'],'row')) {
		unset($_SESSION['captcha'],$post['captcha']); //убиваем капчу чтобы второй раз не отправлялось
		$post['date'] = date('Y-m-d H:i:s');
		$post['text'] = '<p>'.preg_replace("/\n/","<br />",$post['text']).'</p>';
		$post['id'] = mysql_fn('insert','shop_reviews',$post);
		$post['product'] = $product;
		mailer('shop_review',$lang['id'],$post);
		echo 1;
		//перещет рейтинга товара
		$data = array(
			'id' => $product['id'],
			'rating' => mysql_select("SELECT SUM(rating)/COUNT(id) FROM shop_reviews WHERE product=".$product['id'],'string'),
		);
 		mysql_fn('update','shop_products',$data);
	}
	else echo 'error';
}
else {
	require_once(ROOT_DIR.'functions/index_func.php');
	echo html_array('form/message',$message);
}





?>