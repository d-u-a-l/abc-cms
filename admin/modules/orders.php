<?php

//исключение при редактировании модуля
if ($get['u']=='edit') {
	$post['basket'] = serialize($post['basket']);
	if ($post['send']) {
		$result = mysql_query("SELECT name ot_name,text ot_text FROM order_types WHERE id = '".$post['type']."' ORDER BY rank LIMIT 1");
		$order = mysql_fetch_assoc($result);
		$order = array_merge($order,$post);
		$order['id'] = $get['id'];
		$subject = 'Заказ на сайте '.$_SERVER['SERVER_NAME'];
		require_once(ROOT_DIR.'functions/index_func.php');	//функции для сайта
		email($config['email'],$order['email'],$subject,html_array('order/mail',$order));
	}
	unset($post['send']);
}
if ($get['u']=='form') {
	$modules['basket'] = mysql_select("SELECT url FROM pages WHERE module='basket' LIMIT 1",'string');
}

$a18n['name']	= 'статус';
$a18n['login']	= 'пользователь';
$a18n['delivery_type']	= 'дип доставки';
$a18n['delivery_cost']	= 'стоимость';
$a18n['paid']	= 'оплачен';
$a18n['date_paid']	= 'дата оплаты';
$a18n['payment']	= 'способ оплаты';

$table = array(
	'id'	=>	'date',
	'name'	=>	'text',
	'login'	=>	'<a href="/admin.php?m=users&id={user}">{login}</a>',
	'email'	=>	'text',
	'total'	=>	'right',
	'date'	=>	'',
	'payment' => $config['payments'],
	'paid'	=>	'boolean'
);

$where = (isset($get['type']) && $get['type']>0) ? ' AND orders.type='.$get['type'].' ' : '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(orders.email) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(orders.basket) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT orders.*,
		ot.name,
		u.email login
	FROM orders
		LEFT JOIN users u ON  orders.user = u.id
		LEFT JOIN order_types ot ON orders.type = ot.id
	WHERE
		1
		$where
";

$filter[] = array('type',"SELECT ot.id,ot.name FROM order_types ot ORDER BY ot.rank",'-статусы-');
$filter[] = array('search');

$delete = array();

if ($get['id']>0) {
	 $form[] = '<div><a target="_blank" href="/'.$modules['basket'].'/'.$get['id'].'/'.md5($get['id'].$post['date']).'/">Посмотреть заказ на сайте</a></div>';
}

$form[] = array('select td3','type',array(true,"SELECT ot.id,ot.name FROM order_types ot ORDER BY ot.rank"));
$form[] = array('input td3','date',true);
$form[] = array('user td6','user',true);

$form[] = array('select td3','payment',array(true,$config['payments']));
$form[] = array('input td3','date_paid',true);
$form[] = array('checkbox','paid',true);
$form[] = array('checkbox','send','',array('name'=>'отправить уведомление','help'=>'Пользователю будет отправлено письмо со статусом и содержанием заказа'));



$form[] = '<div style="clear:both; background:#E9E9E9; padding:5px 10px; width:875px; margin:0 -10px">';
$form[] = '<table class="product_list">';
$form[] = '<tr data-i="0">';
$form[] = '<th>ID</th>';
$form[] = '<th >название</th>';
$form[] = '<th>количество</th>';
$form[] = '<th>цена</th>';
$form[] = '<th><a href="#" style="background:#35B374; display:inline-block; padding:2px; border-radius:10px"><span class="sprite plus"></span></a></th>';
$form[] = '</tr>';

$template['product'] = '
	<tr data-i="{i}">
		<td><input name="basket[products][{i}][id]" value="{id}" /></td>
		<td><input name="basket[products][{i}][name]" value="{name}" class="product_name"/></td>
		<td><input name="basket[products][{i}][count]" value="{count}" /></td>
		<td><input name="basket[products][{i}][price]" value="{price}" /></td>
		<td><a href="#" class="sprite boolean_0"></a></td>
	</tr>
';
if (isset($post['basket'])) {
	$basket = unserialize($post['basket']); //print_r ($basket);
	if (isset($basket['products']) && is_array($basket['products'])) foreach ($basket['products'] as $key=>$val) {
		$val['i'] = $key;
		$form[] = template($template['product'],$val);
	}
}
$form[] = '</table>';
$form[] = '<div class="clear"></div></div>';
$form[] = array('select td4','basket[delivery][type]',array(@$basket['delivery']['type'],"SELECT od.id,od.name FROM order_deliveries od WHERE display = 1 ORDER BY od.rank"),array('name'=>'доставка'));
$form[] = array('input td4 right','basket[delivery][cost]',@$basket['delivery']['cost'],array('name'=>'стомость доставки'));
$form[] = array('input td4 right','total',true);
$form[] = '<div class="clear"></div>';
$form[] = array('textarea td12','basket[text]',@$basket['text'],array('name'=>'комментарий'));

$form[] = '<h2>Данные клиента</h2>';
$form[] = array('input td3','email',true);
$result = mysql_query("SELECT * FROM user_fields WHERE display = 1 ORDER BY rank DESC");
while ($q = mysql_fetch_assoc($result)) {
	$values = unserialize($q['values']);
	if (!isset($basket['user'][$q['id']][0])) $basket['user'][$q['id']][0] = '';
	if ($q['type']==1) //input
		$form[] = array('input td3','basket[user]['.$q['id'].'][]',$basket['user'][$q['id']][0],array('name'=>$q['name']));
	elseif ($q['type']==2) //select
		$form[] = array('select td3','basket[user]['.$q['id'].'][]',array($basket['user'][$q['id']][0],$values),array('name'=>$q['name']));
	else //textarea
		$form[] = array('textarea td12','basket[user]['.$q['id'].'][]',$basket['user'][$q['id']][0],array('name'=>$q['name']));
}



//шаблоны товара используются для js
$content = '<div style="display:none">';
$content.= '<textarea id="template_product">'.htmlspecialchars($template['product']).'</textarea>';
$content.= '</div>';
$content.= '<style type="text/css">
.form .product_list {width:100%}
.form .product_list th {text-align:left; padding:0 0 5px;}
.form .product_list td {border-top:1px solid #F3F3F3; padding:5px 0; vertical-align:top;}
.form .product_list input {text-align:right; border:1px solid gray; margin:0; padding:0 2px; height:19px; width:70px}
.form .product_list .product_name {width:550px; text-align:left;}
.form .product_list td td {border:none}
</style>';
$content.= '<script type="text/javascript">
$(document).ready(function(){
	$(document).on("click",".product_list th a",function(){
		var i = $(this).parents("table").find("tr:last").data("i");
		i++;
		var content = $("#template_product").val();
		content = content.replace(/{i}/g,i);
		content = content.replace(/{[\w]*}/g,"");
		$(this).parents("table").append(content);
		return false;
	});
	$(document).on("click",".product_list td a",function(){
		$(this).parents("tr").remove();
		return false;
	});
});
</script>';

?>