<?php

if ($get['u']=='edit') {
	$post['parameters'] = (isset($post['parameters']) AND $post['parameters']) ? serialize($post['parameters']) : '';
}

$table = array(
	'_tree'		=>	true,
	'id'		=>	'',
	'name'		=>	'',
	'title'		=>	'',
	'url'		=>	'',
	'display'	=>	'display',
);

$delete = array(
	'confirm'	=>	array(
		'shop_categories'	=>	'parent',
		'shop_products'		=>	'category'
	),
);

$tabs = array(
	1=>'Общее',
	2=>'Настройка параметров',
);

$form[1][] = array('input td8','name',true);
$form[1][] = array('checkbox','display',true);
$form[1][] = array('parent td4 td4','parent',true);
$form[1][] = array('tinymce td12','text',true);
$form[1][] = array('seo','seo url title keywords description',true);
$form[1][] = array('file td6','img','Основная картинка',array(''=>'resize 1000x1000','p-'=>'resize 150x150'));

if ($get['u']=='form' OR $get['id']>0) {
	$form[2][] = 'Параметры добавляются и редактируются в разделе <a href="?m=shop_parameters"><u>параметры</u></a>
		<br />Здесь настраивается только сортировка и отображдение параметров на сайте<br /><br />';
	$parameters = isset($post['parameters']) ? unserialize($post['parameters']) : array();
	$shop_parameters = mysql_select("SELECT id,name FROM shop_parameters ORDER BY rank DESC",'array');
	foreach ($parameters as $k=>$v) {
		if (array_key_exists($k,$shop_parameters)) {
			$parameters[$k]['name'] = $shop_parameters[$k];
			unset($shop_parameters[$k]);
		}
		else unset($parameters[$k]);
	}
	foreach ($shop_parameters as $k=>$v) $parameters[$k] = array('name'=>$v);
	$form[2][] = '<div style="float:left; padding:0 15px 0 155px">в фильтре поиска <a href="#" title="показывать поле поиска по параметру на сайте в фильтре поиска товаров" class="sprite question"></a></div>';
	$form[2][] = '<div style="float:left; width:170px;">на странице товара <a href="#" title="показывать параметр на странице товара" class="sprite question"></a></div>';
	$form[2][] = '<div style="float:left; width:100px;">показывать <a href="#" title="включить/отключить показ везде" class="sprite question"></a></div>';
	$form[2][] = '<ul class="sortable">';
	foreach ($parameters as $k=>$v) {
		$form[2][] = '<li title="для изменения сортировки переместите в нужное место и сохраните">';
		$form[2][] = '<div style="float:left; width:200px">'.$v['name'].'</div>';
		$form[2][] = array('checkbox line td2','parameters['.$k.'][filter]',isset($parameters[$k]['filter']) ? $parameters[$k]['filter'] : '',array('name'=>' '));
		$form[2][] = array('checkbox line td2','parameters['.$k.'][product]',isset($parameters[$k]['product']) ? $parameters[$k]['product'] : '',array('name'=>' '));
		$form[2][] = array('checkbox line td2','parameters['.$k.'][display]',isset($parameters[$k]['display']) ? $parameters[$k]['display'] : '',array('name'=>' '));
		$form[2][] = '</li>';
	}
	$form[2][] = '</ul>';
}