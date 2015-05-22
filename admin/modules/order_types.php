<?php

$table = array(
	'id'		=>	'rank',
	'name'		=>	'',
	'rank'		=>	'',
	'count'		=>	'text',
	'total'		=>	'text',
	'display'	=>	'display'
);

$query = "
	SELECT
		order_types.id,
		order_types.name,
		order_types.rank,
		order_types.display,
		COUNT(orders.id) count,
		SUM(orders.total) total
	FROM order_types
	LEFT JOIN orders ON orders.type = order_types.id
	GROUP BY order_types.id
";

$delete['confirm'] = array('orders'=>'type');

$form[] = array('input td4','name',true);
$form[] = array('input td1','rank',true);
$form[] = array('checkbox','display',true);
$form[] = array('textarea td12','text',true);