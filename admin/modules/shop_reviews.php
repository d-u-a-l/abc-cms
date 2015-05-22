<?php

$table = array(
	'id'		=> 'date:desc id email',
	'date'		=> '',
	'product'	=> '[{product}] {product_name}',
	'rating' 	=> '',
	'name'		=> '',
	'email'		=> '',
	//'text'		=> 'strip_tags',
	'display'	=> 'display'
);

$where = @$_GET['product']>0 ? ' AND product='.intval($_GET['product']) : '';

$query = "
	SELECT shop_reviews.*,sp.name product_name
	FROM shop_reviews
	LEFT JOIN shop_products sp ON sp.id=shop_reviews.product
	WHERE 1 $where
";

$form[] = array('input td3','name',true);
$form[] = array('input td3','email',true);
$form[] = array('input td2','date',true);
$form[] = array('input td1','product',true,array('help'=>'ID товара'));
$form[] = array('input td1','rating',true);
$form[] = array('checkbox','display',true);
$form[] = array('tinymce td12','text',true);