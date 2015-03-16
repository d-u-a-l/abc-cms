<?php
$query = "
	SELECT MIN(price) price_min,MAX(price) price_max
	FROM shop_products
	WHERE display=1 AND category=".$page['id']."
";
$mm = mysql_select($query,'row');
//print_r($mm);
?>
<form method="get" class="form shop_filter clear_form">
<?php
//Цена
echo html_array('form/input2',array(
	'caption'=>i18n('shop|price',true),
	'name'=>'price',
	'value'=>$q['price'],
	'attr'=>array('placeholder="'.$mm['price_min'].'"','placeholder="'.$mm['price_max'].'"')
));

//Производители
if ($brand = mysql_select("
		SELECT sb.id,sb.name
		FROM shop_brands sb, shop_products sp
		WHERE sp.brand = sb.id AND sb.display = 1 AND sp.display=1 AND sp.category=".$page['id']."
		GROUP BY sb.id
		ORDER BY sb.rank DESC, sb.name
	",'array')) {
	echo html_array('form/multi_checkbox',array(
		'caption'=>i18n('shop|brand',true),
		'name'=>'brand',
		'value'=>$q['brand'],
		'data'=>$brand
	));
}
//Параметры

if ($page['parameters']) {	$parameters = unserialize($page['parameters']);
	//print_r($parameters);
	foreach ($parameters as $k=>$v) if (isset($q['shop_parameters'][$k])){
		$name = $q['shop_parameters'][$k]['name'].($q['shop_parameters'][$k]['units'] ? ' ('.$q['shop_parameters'][$k]['units'].')' : '');
		//выбор из вариантов
		if ($q['shop_parameters'][$k]['type']==1) {			$array = unserialize($q['shop_parameters'][$k]['values']);			$query = "
				SELECT DISTINCT p".$k." id, 0 name
				FROM shop_products
				WHERE display=1 AND p".$k." IN ('".implode("','",array_keys($array))."') AND category=".$page['id']."
			";
			//показываем если есть параметры
			if ($values = mysql_select($query,'array')) {
				foreach ($values as $k1=>$v1) if (isset($array[$k1])) $values[$k1]=$array[$k1];
				echo html_array('form/multi_checkbox',array(
					'caption'=>$name,
					'name'=>'p'.$k.'',
					'data'=>$values,
					'value'=>$q['p'.$k],
				));
			}
		}
		//число
		elseif ($q['shop_parameters'][$k]['type']==2) {			$query = "
				SELECT MIN(p".$k.") min,MAX(p".$k.") max
				FROM shop_products
				WHERE display=1 AND category=".$page['id']."
			";
			//показываем если не нули
			if ($mm = mysql_select($query,'row') AND ($mm['min']!=0 OR $mm['max']!=0)) {
					echo html_array('form/input2',array(
					'caption'=>$name,
					'name'=>'p'.$k.'',
					'value'=>$q['p'.$k],
					'attr'=>array('placeholder="'.$mm['min'].'"','placeholder="'.$mm['max'].'"')
				));
			}
		}
		//да/нет
		elseif($q['shop_parameters'][$k]['type']==3)
			echo html_array('form/select',array(
				'caption'=>$name,
				'name'=>'p'.$k.'',
				'select'=>select($q['p'.$k],unserialize($q['shop_parameters'][$k]['values']),''),
			));

	}
}

echo html_array('form/button',array(
	'name' =>	i18n('shop|filter_button'),
));
?>
</form>
