<?php

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

$action = $_GET['action'];

$json['done'] = false;

//добавление товара в корзину
if ($action=='add_product') {
	//id товара
	if ($product = abs(intval(@$_GET['product']))) {
		//количество
		if ($count = abs(intval(@$_GET['count']))) {
			if ($q = mysql_select("
				SELECT *
				FROM shop_products
				WHERE id = '".$product."'
			",'row')) {
				//массив товара для хранения в сессии
				$p = array(
					'id'	=> $q['id'],
					'name'	=> $q['name'],
					//'url'	=> $q['url'],
					'price'	=> $q['price'],
					'count'	=> $count
				);
				//если товар уже есть в корзине
				$is = 0;
				if (isset($_SESSION['basket']['products']) && is_array($_SESSION['basket']['products'])) {
					foreach ($_SESSION['basket']['products'] as $k=>$v) {
						if ($v['id']==$p['id']) {
							$is = 1;
							$_SESSION['basket']['products'][$k]['count']+= $p['count'];
							break;
						}
					}
				}
				//если товара нет, то добавляем новый элемент в массив товаров
				if ($is==0) $_SESSION['basket']['products'][] = $p;
				//прибавляем количество и стоимость
				@$_SESSION['basket']['total']+= $p['price'] * $count;
				@$_SESSION['basket']['count']+= $count;

				$json = array(
					'done'	=>	true,
					'total'	=>	$_SESSION['basket']['total'],
					'count'	=>	$_SESSION['basket']['count'],
				);
			}
			else $json['message'] = 'Такого товара не существует!';

		} else $json['message'] = 'Не указано количество товара!';

	} else $json['message'] = 'Не указан товар!';

//удаление всех товаров в корзине
} elseif ($action=='delete_all') {
	$_SESSION['basket'] = array();
	$json = array(
		'done'	=>	true,
		'total'	=>	0,
		'count'	=>	0,
	);

}

echo json_encode($json);

?>