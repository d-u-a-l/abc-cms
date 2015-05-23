<?php


$a18n['sp_name'] = 'товары';
$a18n['sc_name'] = 'категории';

$table = array(
	'id'		=>	'id n name',
	'img'		=>	'img',
	'name'		=>	'',
	'product'	=>	'<a href="/admin.php?m=shop_products&id={product}">{sp_name}</a>',
	'category'	=>	'<a href="/admin.php?m=shop_categories&id={category}">{sc_name}</a>',
	'n'=>'',
	'display'	=>	'boolean'
);

$join = (isset($get['category']) && $get['category']>0) ? " RIGHT JOIN shop_categories sc2 ON sc2.id = '".intval($get['category'])."' AND sc.left_key>=sc2.left_key AND sc.right_key<=sc2.right_key" : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(sp.name) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(sp.article) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT
		shop_items.*,
		sc.name sc_name,
		sp.name sp_name, sp.category
	FROM shop_items
	LEFT JOIN shop_products sp ON sp.id=shop_items.parent
	LEFT JOIN shop_categories sc ON sp.category = sc.id
	$join
	WHERE 1 $where
";

$filter[] = array('search');
$filter[] = array('category','shop_categories','категории',true);


$form[] = array('input td4','name',true);
$form[] = array('input td2','n',true);
$form[] = array('input td2','parent',true);
$form[] = array('checkbox','display',true);

$form[] = array('file td6','img','Основная картинка',array(''=>'resize 1000x1000','m-'=>'resize 400x400','p-'=>'resize 150x150'));
