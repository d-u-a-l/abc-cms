<?php

$save_as = true;

//ответ аджакса со списком параметров товара при изменении категории
if ($get['u']=='shop_parameters') {
	$parameters = mysql_select("SELECT parameters FROM shop_categories WHERE id=".intval(isset($get['category']) ? $get['category'] : 0),'string');
	$parameters = $parameters ? unserialize($parameters) : array();
	$shop_parameters = mysql_select("SELECT id,name,type,`values`,units FROM shop_parameters ORDER BY rank DESC",'rows_id');
	foreach ($parameters as $k=>$v) if (isset($v['display']) && $v['display']==1){
		$name = $shop_parameters[$k]['name'].($shop_parameters[$k]['units'] ? ' ('.$shop_parameters[$k]['units'].')' : '');
		if (array_key_exists($k,$shop_parameters)) {
			if (!isset($post['p'.$k])) $post['p'.$k] = '';
			if (in_array($shop_parameters[$k]['type'],array(1,3)))
				echo form('select td3','p'.$k,array($post['p'.$k],unserialize($shop_parameters[$k]['values']),''),array('name'=>$name));
			else echo form('input td3','p'.$k,$post['p'.$k],array('name'=>$name));
		}
	}
	die();
}

//поиск сопуствующи товаров
if ($get['u']=='similar_search') {
	$search = stripslashes_smart(@$_GET['value']);
	if ($i=intval($search)) $where = " id=".$i." ";
	else $where = " LOWER(name) LIKE '%".mysql_real_escape_string(mb_strtolower($search,'UTF-8'))."%' OR LOWER(article) LIKE '%".mysql_real_escape_string(mb_strtolower($search,'UTF-8'))."%' ";
	$query = "SELECT * FROM shop_products WHERE ".$where." LIMIT 10";
	if ($products = mysql_select($query,'rows')) {
		foreach ($products as $k=>$v) {
			echo '<li data-id="'.$v['id'].'" title="Перетащите в правую колонку для сохранения">';
			echo $v['img'] ? '<img src="/files/shop_products/'.$v['id'].'/img/a-'.$v['img'].'" />' : '<div></div>';
			echo '<b>'.$v['article'].'</b><br />';
			echo $v['name'].'<br />';
			echo $v['price'].' руб.';
			echo '</li>';
		}
	}
	die();
}

if ($get['u']=='edit') {
	//$post['brand'] = is_array(@$post['brand'])? implode(',',$post['brand']):@$post['brand'];
	//$post['categories'] = is_array(@$post['categories'])? implode(',',$post['categories']):@$post['categories'];
}

$brand = mysql_select("SELECT id,name FROM shop_brands ORDER BY name",'array');

$a18n['sb_name'] = 'производители';
$a18n['sc_name'] = 'категории';

$table = array(
	//'_sorting'	=>	'n',
	'_edit'		=>	true,
	'id'		=>	'date:desc id name price',
	'img'		=>	'img',
	'name'		=>	'',
	'article'	=>	'',
	'brand'		=>	$brand,
	'category'	=>	'<a href="/admin.php?m=shop_categories&id={category}">{sc_name}</a>',
	'date'		=>	'date',
	'price'		=>	'right',
	'price2'	=>	'right',
	'special'	=>	'boolean',
	'market'	=>	'boolean',
	'display'	=>	'boolean'
);

$join = (isset($get['category']) && $get['category']>0) ? " RIGHT JOIN shop_categories sc2 ON sc2.id = '".intval($get['category'])."' AND sc.left_key>=sc2.left_key AND sc.right_key<=sc2.right_key" : "";
$where = (isset($get['brand']) && $get['brand']>0) ? " AND shop_products.brand = '".intval($get['brand'])."' " : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(shop_products.name) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(shop_products.article) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT
		shop_products.*,
		sc.name sc_name
	FROM
		shop_products
	LEFT JOIN shop_categories sc ON shop_products.category = sc.id
	$join
	WHERE shop_products.id>0 $where
";

$filter[] = array('search');
$filter[] = array('brand',$brand,'производители');
$filter[] = array('category','shop_categories','категории',true);

$delete = array(
	'delete'=>"DELETE FROM shop_reviews WHERE product = '".$get['id']."'"  //удаление отзывов
);

$tabs = array(
	1=>'Общее',
	2=>'Параметры',
	3=>'Картинки',
	4=>'Сопутсвующие товары'
);
//если мультиязычный то нужно добавляем вкладки языков кроме главного языка
if ($config['multilingual']) {
	$config['languages'] = mysql_select("SELECT id,name FROM languages ORDEr BY display DESC, rank DESC",'rows');
	if ($get['u']=='edit') {
		//перезапись названия в основной язык
		$k = $config['languages'][0]['id'];
		$post['name'.$k] = $post['name'];
		$post['text'.$k] = $post['text'];
	}
	//вкладку с главным языком не показываем
	foreach ($config['languages'] as $k => $v) if ($k>0) {
		//вкладки
		$tabs['1' . $v['id']] = $v['name'];
		//поля
		$form['1' . $v['id']][] = array('input td12', 'name' . $v['id'], @$post['name' . $v['id']], array('name' => $a18n['name']));
		$form['1' . $v['id']][] = array('tinymce td12', 'text' . $v['id'], @$post['text' . $v['id']], array('name' => $a18n['text']));
	}
}

$form[1][] = array('input td7','name',true);
$form[1][] = array('checkbox','special',true);
$form[1][] = array('checkbox','market',true);
$form[1][] = array('checkbox','display',true);
$form[1][] = array('select td3','brand',array(true,"SELECT id,name FROM shop_brands ORDER BY name"));
$form[1][] = array('select td3','category',array(true,'shop_categories'));
//$form[1][] = array('multicheckbox td3','categories',array(true,'SELECT id,name,level FROM shop_categories ORDER BY left_key'));
$form[1][] = array('input td1 right','price',true);
$form[1][] = array('input td1 right','price2',true);
$form[1][] = array('input td1 right','rating',true,@$get['id']>0?array('name'=>'<a target="_blank" href="?m=shop_reviews&product='.@$get['id'].'">оценки</a>'):NULL);
$form[1][] = array('input td1','article',true);
$form[1][] = array('input td2','date',true);
$form[1][] = array('tinymce td12','text',true);
$form[1][] = array('seo','seo url title keywords description',true);

$form[2][] = '';
if ($get['u']=='form' OR ($get['id']>0 AND $get['u']=='')) {
	$parameters = mysql_select("SELECT parameters FROM shop_categories WHERE id=".intval(isset($post['category']) ? $post['category'] : 0),'string');
	$parameters = $parameters ? unserialize($parameters) : array();
	$shop_parameters = mysql_select("SELECT id,name,type,`values`,units FROM shop_parameters ORDER BY rank DESC",'rows_id');
	$form[2][] = 'Параметры добавляются и редактируются в разделе <a href="?m=shop_parameters"><u>параметры</u></a>.';
	$form[2][] = '<br />Настройка сортировки и отображения параметров на сайте редактируется в разделе <a href="?m=shop_categories"><u>категории</u></a>.
	<br />Здесь редактируются только значения параметров товара.<br /><br />';
	$form[2][] = '<div id="shop_parameters">';
	foreach ($parameters as $k=>$v) if (isset($v['display']) && $v['display']==1){
		$name = $shop_parameters[$k]['name'].($shop_parameters[$k]['units'] ? ' ('.$shop_parameters[$k]['units'].')' : '');
		if (array_key_exists($k,$shop_parameters)) {
			if (!isset($post['p'.$k])) $post['p'.$k] = '';
			if (in_array($shop_parameters[$k]['type'],array(1,3)))
				$form[2][] = array('select td3','p'.$k,array($post['p'.$k],unserialize($shop_parameters[$k]['values']),''),array('name'=>$name));
			else $form[2][] = array('input td3','p'.$k,$post['p'.$k],array('name'=>$name));
		}
	}
	$form[2][] = '</div>';
}

$form[3][] = array('file td6','img','Основная картинка',array(''=>'resize 1000x1000','m-'=>'resize 400x400','p-'=>'resize 150x150'));
$form[3][] = array('file_multi','imgs','Дополнительные картинки',array(''=>'resize 1000x1000','p-'=>'resize 150x150'));

//$form[3][] = array('file_multi_db','shop_items','Дополнительные картинки',array(''=>'resize 1000x1000','preview'=>'resize 150x150'));


$form[4][] = array('input td6','','',array('name'=>'Поиск товаров по названию, артикулу, ID','attr'=>'id="similar_search"'));
$form[4][] = array('input td6','similar',true,array('name'=>'ID сопутсвующих товаров через запятую'));
$form[4][] = '<ul id="similar_results" class="product_list"></ul>';
$form[4][] = '<ul id="similar" class="product_list">';
if (@$post['similar']) {
	$query2 = "SELECT * FROM shop_products WHERE id IN (".$post['similar'].") LIMIT 10";
	if ($products = mysql_select($query2,'rows_id')) {
		$similar = explode(',',$post['similar']);
		foreach ($similar as $k=>$v) if (isset($products[$v])) {
			$form[4][] = '<li data-id="'.$products[$v]['id'].'" title="Перетащите в правую колонку для сохранения">';
			$form[4][] = $products[$v]['img'] ? '<img src="/files/shop_products/'.$products[$v]['id'].'/img/a-'.$products[$v]['img'].'" />' : '<div></div>';
			$form[4][] = '<b>'.$products[$v]['article'].'</b><br />';
			$form[4][] = $products[$v]['name'].'<br />';
			$form[4][] = $products[$v]['price'].' руб.';
			$form[4][] = '</li>';
		}
	}
}
$form[4][] = '</ul>';

$content.= '
<style>
.product_list {float:left; min-height:300px; width:431px; background:#d6d6d6;}
#similar_results {margin:0 13px 0 0;}
.product_list li {clear:both; padding:5px; height:50px; cursor:move}
.product_list li img,
.product_list li div {width:50px; height:50px; float:left; margin:0 5px 0 0}
.product_list li:hover {background:#FFFEDF}
</style>

<script type="text/javascript">
$(document).ready(function(){

	//замена параметров при смене категории
	$(document).on("change",".form select[name=category]",function(){
		var category = $(this).val(),
			id = $(".form").prop("id").replace(/[^0-9]/g,"");
		$.get(
			"/admin.php",
			{"m":"shop_products","u":"shop_parameters","category":category,"id":id},
			function(data){$("#shop_parameters").html(data)}
		);
	});

	//поиск сопуствующих товаров
	$(document).on("keyup","#similar_search",function(e) {
		var value	= $(this).val();
		$.get(
			"/admin.php?m=shop_products&u=similar_search",
			{"value":value},
			function(data){
				$("#similar_results").html(data);
			}
		).fail(function() {
			alert("Нет соединения!");
		});
	});

	similar_results();
	$(document).on("form.open",".form",function(){
		similar_results();
	});

	//сортировка товаров
	function similar_results () {
		$("#similar_results, #similar" ).sortable({
			connectWith: ".product_list",
			stop: function() {
				var similar = [];
				$("#similar li").each(function(){
					similar.push($(this).data("id"));
				});
				$("input[name=similar]").val(similar);
			}
		}).disableSelection();
	}

});
</script>';