<?php

if ($u[2]=='success') {
	$html['content'] = html_array('order/success');
}
elseif ($u[2]=='fail') {
	$html['content'] = html_array('order/fail');
}
else {
	//просмотр заказа по хешу
	if ($u[2]>0) {
		$query = "
			SELECT o.*,ot.name ot_name,ot.text ot_text
			FROM orders o
			LEFT JOIN order_types ot ON ot.id=o.type
			WHERE o.id='".intval($u[2])."'
			LIMIT 1
		"; //echo $query;
		$result = mysql_query($query);
		if (mysql_num_rows($result)==1) {
			$page = $order = mysql_fetch_assoc($result);
			//проверка хеша
			if ($u[3]!=md5($order['id'].$order['date'])) unset($order);
		}
		else $error++;
	}
	//обрабока формы
	elseif (count($_POST)>0) {
		$post = stripslashes_smart($_POST);
		//создание массива корзины
		$q['email'] = strtolower($post['email']);
		$q['total'] = 0;
		$q['count'] = 0;
		$q['user'] = serialize($post['fields']);
		$q['delivery_type'] = abs(intval($post['delivery_type']));
		$q['text'] = $post['text'];
		//создание массива товаров
		if (isset($post['count']) && is_array($post['count'])) {
			foreach ($post['count'] as $k=>$v) {
				if ($v>0 && isset($_SESSION['basket']['products'][$k])) {
					$q['products'][$k] = $_SESSION['basket']['products'][$k];
					$q['products'][$k]['count'] = $v;
					$q['total']+= $q['products'][$k]['price']*$v;
					$q['count']+= $v;
				}
			}
		}
		//стоимость доставки
		$result = mysql_query("SELECT * FROM order_deliveries WHERE id='".$q['delivery_type']."'");
		$d = mysql_fetch_assoc($result);
		$q['delivery_cost'] = $d['free']>0 && $q['total']>$d['free'] ? 0 : $d['cost'];
		if ($q['total']>0) {
			$q['total']+= $q['delivery_cost'];
			$o = mysql_select("SELECT * FROM order_types WHERE display=1 ORDER BY rank LIMIT 1",'row');
			$page = $order = array(
				'paid'	=> 0,
				'type'	=> $o['id'],
				'date'	=> date('Y-m-d H:i:s'),
				'email'	=> $q['email'],
				'total'	=> $q['total'],
				'user'	=> isset($user['id']) ? $user['id'] : 0,
				'basket' => array(
					'products' => $q['products'],
					'delivery' => array(
						'type'=>$q['delivery_type'],
						'cost'=>$q['delivery_cost']
					),
					'user'=> $post['fields'],
					'text'=> $q['text'],
				)
			);
			$order['basket'] = serialize($order['basket']);
			if ($page['id'] = $order['id']=mysql_fn('insert','orders',$order)) {
				$_SESSION['basket']=array();
			}
			$order['ot_name'] = $o['name'];
			$order['ot_text'] = $o['text'];
			require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
			mailer('basket',$lang['id'],$order,$order['email']);
			mailer('basket',$lang['id'],$order);
			//$subject = $lang['basket_order_name'].' № '.$order['id'].' '.$lang['basket_order_from'].' '.date2($order['date'],'%d.%m.%Y');
			//$text = html_array('order/mail',$order);
			//email($config['email'],$config['email'],$subject,$text,$order['email']);
			//email($config['email'],$order['email'],$subject,$text);
		}
	}
	//корзина
	else {
		//удаление товара
		if (isset($_GET['delete'])) {
			if (isset($_SESSION['basket']['products'][$_GET['delete']])) {
				unset ($_SESSION['basket']['products'][$_GET['delete']]);
				//пересчет корзины
				$total = $count = 0;
				foreach ($_SESSION['basket']['products'] as $k=>$v) {
					$count+=$v['count'];
					$total+=$v['price']*$v['count'];
				}
				$_SESSION['basket']['total'] = $total;
				$_SESSION['basket']['count'] = $count;
			}
		}
		$q = isset($_SESSION['basket']) ? $_SESSION['basket'] : array();
		//добавления параметров из настроек пользователя
		if(access('user auth')) {
			$q['user'] = $user['fields'];
			$q['email'] = $user['email'];
		}
	}


	//шаблон заказа
	if (isset($order)) {
		$html['content'] = html_array('order/text',$order);
	}
	//шаблон корзины
	else {
		$html['content'] = html_array('order/basket',@$q);
	}
}
?>