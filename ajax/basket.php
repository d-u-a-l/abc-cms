<?php

session_start();

$action = $_GET['action'];

$json['done'] = false;

//добавление товара в корзину
if ($action=='add_product') {
	//id товара
	if ($product = abs(intval(@$_GET['product']))) {
		//количество
		if ($count = abs(intval(@$_GET['count']))) {
			require_once(ROOT_DIR.'config_db.php'); //доступ к ДБ
			$result = mysql_query("
				SELECT *
				FROM shop_products
				WHERE id = '".$product."'
			");
			if ($q = mysql_fetch_assoc($result)) {
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

			} else $json['message'] = 'Такого товара не существует!';

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