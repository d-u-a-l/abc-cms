<?php

if ($u[3]) {//одна запись
	$query = "
		SELECT o.*,ot.name ot_name,ot.text ot_text
		FROM orders o
		LEFT JOIN order_types ot ON ot.id = o.type
		WHERE o.user=".$user['id']." AND o.id = '".intval($u[3])."'
		LIMIT 1
	";
	$result = mysql_query($query);
	if ($page = mysql_fetch_assoc($result)) {
		$page['name'] = $page['title'] = $page['keywords'] = $page['description'] = i18n('basket|order_name').' № '.$page['id'];
		$breadcrumb['module'][] = array($page['name'],$page['id']);
		$html['content'] = html_array('order/text',$page);
	}
	else $error++;
}
else {//список записей
	$query = "
		SELECT o.*,ot.name ot_name
		FROM orders o
		LEFT JOIN order_types ot ON ot.id = o.type
		WHERE o.user = '".$user['id']."'
		ORDER BY o.date DESC
	"; //echo $query
	$html['content'] = html_query('order/list normal',$query,'у вас нет заказов');
}

?>