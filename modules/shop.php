<?php
$html['filter'] = $html['category_list'] = $html['product_list'] = '';
$product = $category = false;

if ($u[2] AND $id = intval(explode2('-',$u[2])) AND $category = mysql_select("SELECT * FROM shop_categories WHERE id = '".$id."'LIMIT 1",'row')) {
	$page = array_merge($page,$category);
	$query = "
		SELECT id,name,url
		FROM shop_categories
		WHERE left_key <= ".$page['left_key']." AND right_key >= ".$page['right_key']."
		ORDER BY left_key DESC
	";
	//вложенный breadcrumb
	$breadcrumb['module'] = breadcrumb ($query,'/'.$modules['shop'].'/{id}-{url}/');
	// ТОВАР *******************************************************************
	if ($u[3]) {
		$id = intval(explode2('-',$u[3]));
		//запрос на товар и на категорию
		$product = mysql_select("
			SELECT sp.*,sb.name brand_name
			FROM shop_products sp
			LEFT JOIN shop_brands sb ON sb.id=sp.brand
			WHERE sp.display = 1 AND sp.id = '".$id."'
			LIMIT 1
		",'row');
		if ($product) {
			$page = array_merge($page,$product);
			$page['parameters'] = $category['parameters'];
			//переадреация на корректный урл
			if (explode2('-',$u[3],2)!=$page['url']) die(header('location: /'.$modules['shop_products'].'/'.$page['id'].'-'.$page['url'].'/',true,301));
			$html['content'] = html_array('shop/product_text',$page);
			$breadcrumb['module']	= array_merge(
				array(array($page['name'],'/'.$modules['shop'].'/'.$category['url'].'/{id}-{url}/')),
				$breadcrumb['module']
			);
		}
		else $error++;
	}
	//КАТЕГОРИЯ
	else {
		$query = "
			SELECT *
			FROM shop_categories
			WHERE parent = '".$page['id']."' AND display = 1
			ORDER BY left_key
		";
		//список подкатегорий
		if ($content = html_query('shop/category_list',$query,'','')) {
			$html['category_list'] = $content;
		}
		//список товаров если нет подкатегорий
		else {
			//загрузка функций для формы
			require_once(ROOT_DIR.'functions/index_form.php');
			//определение значений формы
			$fields = array(
				'brand'	=> 'string_int',
				'price'	=> 'min_max',
			);
			$shop_parameters = false;
			if ($page['parameters']) {
				$prms=array();
				foreach (unserialize($page['parameters']) as $k=>$v) if (@$v['display'] AND @$v['filter']) $prms[]=$k;
				if ($shop_parameters = mysql_select("
					SELECT * FROM shop_parameters
					WHERE display=1 AND id IN('".implode("','",$prms)."')
					ORDER BY rank DESC",'rows_id')) {
					foreach ($shop_parameters as $k=>$v) {						if($v['type']==1) $fields['p'.$k] = 'string_int';
						elseif($v['type']==2) $fields['p'.$k] = 'min_max';
						elseif($v['type']==3) $fields['p'.$k] = 'boolean';						//else $fields['p'.$k] = 'int';
					}
				}
			}

			//создание массива $post
			$post = form_smart($fields,stripslashes_smart($_GET)); //print_r($post);
			$post['shop_parameters'] = $shop_parameters;

			$where = '';
			if ($post['brand']) $where.=" AND sp.brand IN ('".$post['brand']."')";
			if ($post['price'] AND $price=explode('-',$post['price'])) {
				if ($price[0]>0) $where.= " AND sp.price>=".$price[0];
				if ($price[1]>0) $where.= " AND sp.price<=".$price[1];
			}
			if ($page['parameters'] AND $shop_parameters) {
				foreach ($shop_parameters as $k=>$v) {					//мультичекбокс
					if ($v['type']==1) {						if($post['p'.$k]!='')
							$where.= " AND sp.p".$k." IN (".$post['p'.$k].")";
					}
					//число от и до
					elseif ($v['type']==2) {						if($post['p'.$k]!='0-0') {
							$min_max = explode('-',$post['p'.$k]);
							if ($min_max[0]>0) $where.= " AND sp.p".$k.">=".$min_max[0];
							if ($min_max[1]>0) $where.= " AND sp.p".$k."<=".$min_max[1];
						}
					}
					//да/нет
					elseif($post['p'.$k]!='') {
						$where.= " AND sp.p".$k." = '".$post['p'.$k]."'";
					}
				}
			}
			//фильтр
			$html['filter'] = html_array('shop/product_filter',$post);
			//список товаров
			$query = "
				SELECT sp.*, CONCAT('".mysql_real_escape_string($page['url'])."') category_url
				FROM shop_products sp
				WHERE sp.display = 1
					AND sp.category=".$page['id']."
					$where
				GROUP BY sp.id
				ORDER BY sp.price
			"; //echo $query;
			$html['product_list'] = html_query('shop/product_list shop',$query);
		}
	}
}
//главная страница модуля
else {
	$html['category_list'] = html_query('shop/category_list',"
		SELECT *
		FROM shop_categories
		WHERE level = 1 AND display = 1
		ORDER BY left_key
	");
}

?>