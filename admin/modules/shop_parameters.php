<?php

$config['shop_parameters']['type'] = array(
	1 => 'выбор из вариантов',
	2 => 'число',
	3 => 'чекбокс',
);

$decimal = array(0,1,2,3);

$post['type'] = array_key_exists(isset($post['type']) ? $post['type'] : 0,$config['shop_parameters']['type']) ? $post['type'] : 1;
if ($get['u']=='edit') {
	if (in_array($post['type'],array(1))) {
		if (isset($post['values']['select'])) {
			if (is_array($post['values']['select']))
				foreach ($post['values']['select'] as $k=>$v) if ($v=='') unset($post['values']['select'][$k]);
			$post['values'] = serialize($post['values']['select']);
		}
		else $post['values'] = '';
	}
	elseif (in_array($post['type'],array(2))) {
		$post['values'] = isset($post['values']['decimal']) ? intval($post['values']['decimal']) : 0;
		$post['values'] = in_array($post['values'],$decimal) ? $post['values'] : 0;
	}
	elseif (in_array($post['type'],array(3)))
		$post['values'] = (isset($post['values']['checkbox']) && $post['values']['checkbox']) ? serialize($post['values']['checkbox']) : '';
	else $post['values'] = '';

	if ($get['id']=='new') {
		$get['id'] = mysql_fn('insert',$get['m'],$post);
		if (in_array($post['type'],array(1,3)))
			mysql_query('ALTER TABLE  `shop_products` ADD  `p'.$get['id'].'` INT UNSIGNED NOT NULL, ADD INDEX (  `p'.$get['id'].'` )');
		elseif($post['type']==2)
			mysql_query('ALTER TABLE  `shop_products` ADD  `p'.$get['id'].'` DECIMAL( 10,'.$post['values'].') NOT NULL, ADD INDEX (  `p'.$get['id'].'` )');
	}
	else {
		if (in_array($post['type'],array(1,3)))
			mysql_query('ALTER TABLE  `shop_products` CHANGE  `p'.$get['id'].'` `p'.$get['id'].'` INT UNSIGNED NOT NULL');
		elseif($post['type']==2)
			mysql_query('ALTER TABLE  `shop_products` CHANGE  `p'.$get['id'].'`  `p'.$get['id'].'` DECIMAL( 10,'.$post['values'].') NOT NULL');
	}
}

$delete['delete'] = array('ALTER TABLE `shop_products` DROP `p'.$get['id'].'`','ALTER TABLE shop_products DROP INDEX p'.$get['id'].'');

$a18n['type']			= 'тип';

$table = array(
	'id'	=>	'rank:desc name id',
	'name'	=>	'',
	'units'	=> '',
	'rank'	=>	'',
	'type'	=>	$config['shop_parameters']['type'],
	'display' => 'display'
);

$form[] = array('input td6','name',true);
$form[] = array('input td2','units',true);
$form[] = array('input td2','rank',true);
$form[] = array('checkbox','display',true);
$form[] = array('select td6','type',array(true,$config['shop_parameters']['type']));

$form[] = '<div class="clear"></div>';

$template['select'] = '
<li class="field input">
	<a href="#" class="sprite delete"></a>
	<input name="values[select][{i}]" value="{value}">
</li>
';
$template['checkbox'] = '
<div class="field input td2">
	<label>Да</label>
	<div><input name="values[checkbox][1]" value="{yes}"></div>
</div>
<div class="field input td2">
	<label>Нет</label>
	<div><input name="values[checkbox][2]" value="{no}"></div>
</div>

';
if ($get['u']=='form' OR $get['id']>0) {
	$values = (isset($post['values']) && $post['values']) ? unserialize($post['values']) : array();
	if(!is_array($values)) $values=array();
	//выбор из вариантов
	$form[] = '<div data-type="select" class="parameter_values"'.(in_array($post['type'],array(1)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">В товаре можно будет выбирать значения из указанных здесь вариантов.</div>';
	$form[] = '<b>Значения параметров:</b> &nbsp; ';
	$form[] = '<input name="values[select][0]" type="hidden" value="" />'; //индекс 0 по умолчанию пустой чтобы не создавался
	$form[] = '<a href="#" class="plus button green"><span><span class="sprite plus"></span>добавить вариант</span></a>';
	$form[] = '<ul class="sortable">';
	foreach ($values as $k=>$v) $form[] = template($template['select'],array('i'=>$k,'value'=>$v));
	if(count($values)<2) for ($i=count($values); $i<2; $i++) $form[] = template($template['select'],array('i'=>'','value'=>''));
	$form[] = '</ul>';
	$form[] = '</div>';

	$form[] = '<div data-type="checkbox" class="parameter_values"'.(in_array($post['type'],array(3)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">Укажите варинаты да/нет для товара (например, есть/нет, присутсвует/отсутсвует и т.д.)</div>';
	$form[] = template($template['checkbox'],array('yes'=>isset($values[1]) ? $values[1] : '','no'=>isset($values[2]) ? $values[2] : ''));
	$form[] = '</div>';

	$form[] = '<div data-type="decimal" class="parameter_values"'.(in_array($post['type'],array(2)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">Данный параметр будет чисельный и в фильтре поиска товаров будет возможность фильтровать товары от минимального до максимального значения данного параметра</div>';
	$form[] = array('select td3','values[decimal]',array(isset($post['values']) ? $post['values'] : '',$decimal),array('name'=>'количество нулей после запятой'));
	$form[] = '</div>';
}

$content = '
<div style="padding:5px 0 0; color:#999; font-size:11px">Здесь настраиваются только сами параметры. Настройка отображения и сортировки параметров настраиваются в <a href="/admin.php?m=shop_categories">разделах каталога</a>.</div>
<div style="display:none">
<textarea id="template_select">'.htmlspecialchars($template['select']).'</textarea>
</div>
<style>
.parameter_values li {padding:2px 13px; float:none;}
.parameter_values li.field input {width:830px}
.parameter_values li.field {min-height:auto;}
.parameter_values li.field a {float:right; margin:2px 0 0}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(document).on("change","select[name=\'type\']",function(){
		$(".parameter_values").hide();
		var type = $(this).val();
		if (type==1) $(".parameter_values[data-type=\'select\']").show();
		if (type==2) $(".parameter_values[data-type=\'decimal\']").show();
		if (type==3) $(".parameter_values[data-type=\'checkbox\']").show();
		return false;
	});
	$(document).on("click",".parameter_values .plus",function(){
		var content = $("#template_select").val();
		content = content.replace(/{[^}]*}/g,"");
		$(this).next("ul").append(content);
		$("ul.sortable").sortable();
		return false;
	});
	$(document).on("click",".parameter_values .delete",function(){
		$(this).parent("li").remove();
		return false;
	});
});
</script>
';