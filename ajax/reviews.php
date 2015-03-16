<?php

session_start();

require_once(ROOT_DIR.'functions/common_func.php');
require_once(ROOT_DIR.'functions/common_conf.php');
require_once(ROOT_DIR.'functions/index_conf.php');
require_once(ROOT_DIR.'functions/index_func.php');
require_once(ROOT_DIR.'functions/index_form.php'); //загрузка функций для формы

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
	require_once(ROOT_DIR.'config_db.php'); //доступ к ДБ
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