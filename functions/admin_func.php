<?php

/**
 * функции админпанели
 */

/**
 * кнопка удаления записи из БД и всех её файлов
 * @param string $delete
 * @return string
 * @see $delete
 */
function html_delete($delete='') {
	global $get,$a18n;
	$content = '';
	if ($get['id']>0 && is_array($delete)) {
		//если есть связанные записи
		if (isset($delete['confirm'])) {
			if (is_array($delete['confirm'])) foreach ($delete['confirm'] as $k=>$v) {
				if (strpos($v, ' ')) { //запрос
					if (mysql_select($v,'row')) $content.= '[связанные '.a18n($k).'] ';
				} else {
					$query = 'SELECT `id` FROM `'.$k.'` WHERE `'.$v.'` = '.$get['id'];
					if (mysql_select($query,'row')) {
						if (array_key_exists($k, $a18n))
							$content .= '<a href="admin.php?m='.$k.'&'.$v.'='.$get['id'].'">['.a18n($k).']</a> ';
						else $content .= 'есть связи';
					}
				}
			}
		}
		//если есть связанные записи
		if ($content) return 'удаление невозможно: '.$content;
	}
}


/**
 * функция вывода строк таблицы
 * @param array $table - массив колонок таблицы
 * @param array $q - массив данных ряда
 * @param bool $head - вернуть шапку или ряд
 * @return string - ряд <tr>
 * @see $table
 */
function table_row($table,$q,$head = false) {
	global $config,$url;
	if (!isset($table['_edit'])) $table = array_merge(array('_edit'=>true),$table);
	elseif ($table['_edit']==false) unset($table['_edit']);
	if (!isset($table['_delete'])) $table['_delete'] = true;
	elseif ($table['_delete']==false) unset($table['_delete']);
	$content = '';
	//ШАПКА ТАБЛИЦЫ
	if ($head) foreach ($table as $k=>$v) {
		if ($k=='_tree') $content.= '<th class="colspan" style="padding:0 0 0 10px"><span class="sprite tree" title="дерево вложенности"></span></th>';
		elseif ($k=='_sorting') $content.= '<th class="colspan"><span class="sprite sorting" title="сортировка"></span></th>';
		elseif ($k=='_edit') $content.= '<th style="padding:0; text-align:center"><a class="sprite plus2 open" href="/admin.php?'.$url.'id=new" title="добавить новую запись"></a></th>';
		elseif ($k=='_delete') $content.= '<th width="20px"></th>';
		elseif ($k=='display') $content.= '<th></th>';
		elseif ($v=='boolean') $content.= '<th></th>';
		elseif ($v=='img') $content.= '<th></th>';
		else {
			global $get;
			//$fieldset[$k]  = isset($fieldset[$k]) ? $fieldset[$k] : $k; //если нет $fieldset называем ключом
			$content.= '<th>';
			if (isset($q['sort_array']) && array_key_exists($k,$q['sort_array'])) {
				if ($q['order']==$k) {
					if ($get['s']) $s = ($get['s']=='desc') ? 'asc' : 'desc';
					else $s = $q['sort_array'][$k];
					$a = $s=='asc' ? ' desc' : ' asc';
				}
				else {
					$s = $q['sort_array'][$k];
					$a = ' none '.$s;
				}
				$content.= '<a class="sort'.($q['order']==$k ? ' active' : '').'" href="?'.$url.'o='.$k.'&s='.$s.'"><span class="sprite '.$a.'"></span>'.a18n($k).'</a>';
			}
			else $content.= a18n($k);
			$content.= '</th>';
		}
	}
	//РЯД ТАБЛИЦЫ
	else foreach ($table as $k=>$v) {
		if ($v && !is_array($v)) {
			preg_match_all('/{(.*?)}/',$v,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key=>$val) $matches[1][$key] = isset($q[$val]) ? $q[$val] : '';
			$v = str_replace($matches[0],$matches[1],$v);
		}
		if ($k=='_edit')		$content.= '<td align="center"><a href="/admin.php?'.$url.'id='.$q['id'].'" class="sprite edit open"></a></td>';
		elseif ($k=='_tree')	$content.= '<td class="level"><span class="sprite level item"></span></td>';
		elseif ($k=='_sorting')	$content.= '<td><span class="sprite sorting"></span></td>';
		elseif ($k=='_delete')	$content.= '<td align="center"><a class="sprite delete" href="#"></a></td>';
		elseif ($k=='id')		$content.= '<td align="right"><b>'.$q[$k].'</b></td>';
		elseif (is_array($v))	$content.= '<td><b>'.(isset($v[$q[$k]]) ? $v[$q[$k]] : '').'</b></td>';
		elseif ($v=='date')		$content.= '<td data-name="'.$k.'" class="post">'.$q[$k].'</td>';
		elseif ($v=='boolean' OR $v=='display') {
			$key = in_array($k,$config['boolean']) ? $k : 'boolean';
			$content.= '<td align="center" data-name="'.$k.'" data-key="'.$key.'">';//key - клас спрайта для иконки
			$content.= '<a class="sprite '.$key.'_'.($q[$k]==1 ? '1' : '0').' js_boolean" href="#" title="'.a18n($k).'"></a>';
			$content.= '</td>';
		}
		elseif ($v=='right')	$content.= '<td data-name="'.$k.'" align="right" class="post">'.$q[$k].'</td>';
		elseif ($v=='text')		$content.= '<td data-name="'.$k.'"><b>'.$q[$k].'</b></td>';
		elseif ($v=='img')		$content.= '<td align="center" data-name="'.$k.'">'.($q[$k] ? '<a onclick="return hs.expand(this)" href="/files/'.$_GET['m'].'/'.$q['id'].'/'.$k.'/'.$q[$k].'"><img class="img" src="/files/'.$_GET['m'].'/'.$q['id'].'/'.$k.'/a-'.$q[$k].'" /></a>' : '').'</td>';
		elseif ($v=='')			$content.= '<td data-name="'.$k.'" class="post">'.(isset($q[$k]) ? $q[$k] : '').'</td>';
		else					$content.= '<td>'.$v.'</td>';
	}
	return $content;
}


/**
 * функция формирования нтмл кода таблицы в админке
 * @param array $table - массив колонок таблицы
 * @param string $query - запрос
 * @return string - нтмл код таблицы
 * @see $table, table_row()
 */
function table ($table,$query='') {
	global $url,$get,$filter;
	$array_count	= array(20,50,100,'all');
	$begin			= $get['b']>0 ? intval($get['b']).',' : '';
	$count			= in_array($get['c'],$array_count) ? $get['c'] : $array_count[0];
	$sorting		= explode(' ',$table['id']);
	foreach ($sorting as $s) {
		$s = explode(':',$s);
		$sort_array[$s[0]] = (isset($s[1]) && $s[1]=='desc') ? 'desc' : 'asc';
	}
	$tree = array_key_exists('_tree',$table);
	$sorting = array_key_exists('_sorting',$table);
	//ГЕНЕРАЦИЯ $query
	if ($query=='') {
		$query = "SELECT ";
		if ($tree) $query.= $get['m'].'.level,'.$get['m'].'.parent,';
		foreach ($table as $k=>$v) if ($k[0]!='_') $query.= '`'.$k.'`,';
		$query = substr($query,0, -1);
		$query.= " FROM ".$get['m']." WHERE 1";
		//если есть фильтр (например, для языка)
		if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) if (isset($get[$v[0]])){
			$query.= " AND ".$get['m'].".".$v[0]." = ".intval($get[$v[0]]);
		}
	}
	//НАСТРОЙКА СОРТИРОВКИ
	//деревовидный список
	$th = array();
	if ($tree) {
		$order = $get['m'].".left_key";
		$sort  = '';
	//сортировка
	} elseif ($sorting) {
		$order = $get['m'].'.'.$table['_sorting'];
		$sort  = '';
	//обычный список
	} else {
		$th['order'] = $order = ($get['o'] && array_key_exists($get['o'],$table)) ? $get['o'] : key($sort_array);
		if (!$get['s']) $get['s'] = $sort_array[$order];
		$sort = ($get['s']=='desc') ? 'DESC' : 'ASC';
		$th['sort_array'] = $sort_array;
	}
	$paginator = pagination($query,$array_count,$count);
	$query.= ' ORDER BY '.$order.' '.$sort.',id '.$sort;
	if ($get['c']!='all') $query.= ' LIMIT '.$begin.$count;
	$content = '<div id="table">';
	$content.= '<div class="pagination corner_top">'.$paginator.'</div>';
	$content.= '<div class="clear"></div>';
	$content.= '<table cellspacing="1" cellpadding="0" class="table'.($tree ? ' tree' : '').($sorting ? ' sortable' : '').'" data-module="'.$get['m'].'">';
	$content.= '<thead>';
	$content.= '<tr data-id="new" class="head">';
	$content.= table_row($table,$th,true); //шапка таблицы
	$content.= '</tr>';
	$content.= '</thead>';
	$content.= '<tbody>';
	$i = 0;
	$result = mysql_query($query);
	if ($error = mysql_error()) trigger_error($error.' '.$query, E_USER_DEPRECATED);
	else {
		while ($q = mysql_fetch_assoc($result)) {
			$i++;
			$tr = fmod($i,2)==0 ? 'even' : 'odd';
			if ($tree) $content.= '<tr class="'.$tr.'" data-parent="'.$q['parent'].'" data-level="'.$q['level'].'" data-id="'.$q['id'].'">';
			elseif ($sorting) $content.= '<tr class="'.$tr.'" data-sorting="'.$q[$table['_sorting']].'" data-id="'.$q['id'].'">';
			else $content.= '<tr class="'.$tr.'" data-id="'.$q['id'].'">';
			$content.= table_row($table,$q);
			$content.= '</tr>';
		}
	}
	$content.= '</tbody>';
	$content.= '</table>';
	$content.= '<div class="pagination corner_bottom">'.$paginator.'</div>';
	$content.= '</div>';
	return $content;
}

//пагинатор
function pagination ($query,$array_count,$count) {//04.02.10 pagination (SELECT * FROM * WHERE *; 100) - группирует результаты запроса по 100 на страницу
	//$query - ("SELECT id,name FROM page ORDER BY rank DESC") - sql-запрос на вывод всего меню
	//$count - число - количество записей на страницу
	$begin = intval(@$_GET['b']);
	$num_rows = mysql_select($query,'num_rows');
	if ($count=='all') $paginator[] = array(1,0);
	elseif ($num_rows>0) {
		if ($count>$num_rows) $count=$num_rows;
		$amount_list = ceil($num_rows/$count); //количство страниц
		$page = ($begin+$count)/$count;
		//меньше текущей страницы
		for ($i=1; $i<$page; $i++) {
			if (
				$i==1
				OR //меньше 10
				($i+5>$page)
				OR //меньше 100
				($i+100>$page AND fmod($i,10)==0)
				OR //меньше 1000
				($i+1000>$page AND fmod($i,100)==0)
			) {
				$ii = $i*$count-$count;
				$paginator[] = array($i,$ii);
			}
		}
		//больше текущей страницы
		for ($i=$page; $i<=$amount_list; $i++) {
			if (
				$i==$amount_list
				OR //больше 10
				($i-5<$page)
				OR //больше 100
				($i-100<$page AND fmod($i,10)==0)
				OR //больше 1000
				($i-1000<$page AND fmod($i,100)==0)
			) {
				$ii = $i*$count-$count;
				$paginator[] = array($i,$ii);
			}
		}
	}
	$content = '<div class="pagination_count">';
	$content.= '<span></span>';
	$content.= '<div><select onchange="top.location=\'admin.php?'.build_query('c,u,id').'&c=\'+this.value;">';
	foreach ($array_count as $k=>$v) {
		$s = (isset($_GET['c']) && $_GET['c']==$v) ? ' selected="selected"' : '';
		$content.= '<option value="'.$v.'"'.$s.'>'.$v.'</option>';
	}
	$content.= '</select></div>';
	$content.= '</div>';
	$content.= '<div class="pagination_pages">';
	$content.= '<span>'.a18n('pagination_page').'</span><ul>';
	if (isset($paginator) && is_array($paginator)) foreach ($paginator as $k=>$v) {
		$a = ($begin==$v[1]) ? ' class="active"' : '';
		//$content.= '<li><table><tbody><tr><td><a href="?'.build_query('b').'&b='.$v[1].'"'.$a.'>'.$v[0].'</a></td></tr></tbody></table></li>';
		$content.= '<li><a href="?'.build_query('b,u,id').'&b='.$v[1].'"'.$a.'>'.$v[0].'</a></li>';
	}
	$content.= '</ul></div>';
	$content.='<div class="clear"></div>';
	return  $content;
}

//фильтр поиска
function filter ($key,$query='',$default='',$clear=false) {
	//ситнаксис аналогичен select
	//$key		- ключ из $_GET
	//$query	- sql-запрос либо массив для селекта либо модуль
	//$default	- значени по умолчанию
	//$clear	- очищать урл
	global $get;
	if ($clear==false) $url=build_query($key);
	else $url = 'm='.$_GET['m'];
	if ($query!='') {
		$content = select (isset($get[$key]) ? $get[$key] : '',$query,$default);
		$content = '<div class="filter"><select name="'.$key.'" onchange="top.location=\'admin.php?'.$url.'&'.$key.'=\'+this.value;">'.$content.'</select></div>';
	}
	else {
		$content = '<div class="filter" style="float:right"><input placeholder="Поиск" name="'.$key.'" value="'.htmlspecialchars(stripslashes_smart(isset($_GET[$key]) ? $_GET[$key] : '')).'" /><a class="sprite search" href="admin.php?'.$url.'&'.$key.'="></a></div>';
	}
	return $content;
}

//поля формы
function form ($class,$key,$value,$param=array('attr'=>'','name'=>'')) {  //19.02.12 различные элементы формы для записи в БД
	//$class  - класс поля
	//$key   - ключ
	//$value  - чистые или обработанные постданные
	//$style - style=>'стиль для поля', name=>'имя блока', 'box'=>'стиль для блока'
	global $user,$get,$filter,$a18n,$config; //массив с названиями блоков
	$name	= empty($param['name']) ? a18n($key) : $param['name'];//название по умолчанию указано в массиве $fieldset
	$type	= current(explode(' ',$class));
	if ($value===true OR (is_array($value) AND $value[0]===true)) {
		global $post;
		if (in_array($type,array('select','multicheckbox'))) $value[0] = isset($post[$key]) ? $post[$key] : '';
		elseif(in_array($type,array('parent','seo'))) $value = isset($post) ? $post : array();
		else $value = isset($post[$key]) ? $post[$key] : '';
	}
	//if (is_array($value) AND $value[0]==true)
	$title	= isset($param['title']) ? ' title="'.$param['title'].'"' : '';
	$help	= isset($param['help']) ? '<a href="#" class="sprite question" title="'.$param['help'].'"></a>' : '';
	$label	= '<label'.$title.'><span>'.$name.'</span>'.$help.'</label>';
	$content = '';
	$param['attr'] = isset($param['attr']) ? $param['attr'] : '';
	if ($type=='input') {
		$content = $label.'<div><input name="'.$key.'" '.$param['attr'].' value="'.htmlspecialchars($value).'" /></div>';
	} elseif ($type=='select') {
		$content = $label.'<div><select name="'.$key.'" '.$param['attr'].'>'.select($value[0],isset($value[1]) ? $value[1] : '',isset($value[2]) ? $value[2] : NULL).'</select></div>';
	} elseif ($type=='textarea') {
		$content = $label.'<div><textarea cols="1" rows="1" name="'.$key.'" '.$param['attr'].'>'.htmlspecialchars($value).'</textarea></div>';
	} elseif ($type=='text') {
		$content = $label.'<div '.$param['attr'].'>['.$value.']</div>';
	} elseif ($type=='user') {
		$value = (isset($value) && $value) ? $value : $user['id'];
		$q = mysql_select("SELECT u.id,u.email login FROM users u WHERE u.id=".intval($value),'row');
		$content = $label.'<input name="'.$key.'" type="hidden" value="'.$q['id'].'" /><div '.$param['attr'].'><a href="?m=users&id='.$q['id'].'">['.$q['login'].']</a></div>';
	} elseif ($type=='checkbox') {
		$checked = $value==1 ? 'checked="checked"' : '';
		$content = '<input type="hidden" name="'.$key.'" value="0" /><label><input type="checkbox" name="'.$key.'" value="1" '.$checked.' '.$param['attr'].' /><span>'.$name.'</span>'.$help.'</label>';
	} elseif ($type=='tinymce') {
		$rand = rand(100000,999999);
		$content = $label.'<div><textarea id="'.$rand.'" cols="1" rows="1" '.$param['attr'].' name="'.$key.'">'.$value.'</textarea></div><div class="clear"></div>';
		$content.= '
<script type="text/javascript">
tinymce.init({
	selector: "#'.$rand.'",
	language: "'.$config['admin_lang'].'",
	plugins: [
		"advlist autolink lists link image charmap anchor",//preview  print
		"visualblocks code",//fullscreen
		"media table contextmenu paste textcolor moxiemanager"//
	],
	/*
	toolbar: "undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | anchor link image | table | code template",
	plugins: [
		"advlist autolink link image lists charmap hr anchor pagebreak spellchecker",
		"searchreplace visualblocks visualchars code insertdatetime media nonbreaking",
		"table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern moxiemanager"
	],
	*/
	toolbar1: "bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | styleselect fontselect fontsizeselect | bullist numlist ",
	toolbar2: "undo redo | link unlink anchor image media code | table | removeformat | subscript superscript | charmap emoticons | visualchars visualblocks nonbreaking | outdent indent blockquote",
	menubar: false,
	style_formats: [
		{title: "Заголовок", block: "h2"},
		{title: "Example", block: "div", classes: "example"},
	],
	content_css : "/templates/css/tinymce.css?'.filemtime(ROOT_DIR.'templates/css/tinymce.css').'",
	relative_urls: false,
	setup : function(ed) {
		ed.on("blur", function(e) {
			$(".form").data("changed",true);
		});
	}
});
</script>
		';
	}
	elseif ($type=='multicheckbox') {
		$val = is_array($value[0]) ? $value[0] : explode(',',$value[0]);
		$data = is_array($value[1]) ? $value[1] : mysql_select($value[1],'rows');
		//print_r($data);
		$content = '<label class="all">отметить все<input type="checkbox" onchange="$(this).closest(\'.multicheckbox\').find(\'input\').prop(\'checked\',this.checked)" /></label>';
		$content.= $label;
		if (is_array($data) AND count($data)>0) {
			$level = -1;
			$curr = current($data);
			$slevel = isset($curr['level']) ? $curr['level'] : 0;
			foreach ($data as $k=>$v) {
				if (!isset($v['level'])) $v['level'] = 0;
				if ($level>=$v['level'] ) $content.= '</li>';
				if ($level>$v['level']) $content.= '</ul></li>';
				if ($level<$v['level']) $content.= '<ul class="l'.$v['level'].'">';
				$checked = in_array($v['id'],$val) ? 'checked="checked"' : '';
				$class2 = in_array($v['id'],$val) ? ' class="checked"' : '';
				$content.= '<li><label'.$class2.'>'.($v['id'] ? '<input name="'.$key.'[]" type="checkbox" value="'.$v['id'].'"'.$checked.' />' : '').'<span>'.iconv_substr($v['name'],0,112,"UTF-8").'</span></label>';
				$level = $v['level'];
			}
			for ($i=$slevel; $i<=$v['level']; $i++) $content.= '</li></ul>';
		}
		else $content.= '<ul class="l0"></ul>';
		$checked = $val==1 ? ' checked="checked"' : '';
	}
	elseif ($type=='seo') {
		$content = '<div class="seo-optimization"><a href="#">'.a18n('seo_optimization').'</a></div>';
		$content.= '<div style="display:none">';
		$content.= form('checkbox td3','seo',isset($value['seo']) ? $value['seo'] : '',array('name'=>a18n('seo_generate')));
		foreach (explode(' ',$key) as $k) {
			switch ($k) {
				case 'url':			$content.= form('input td9','url',isset($value['url']) ? $value['url'] : ''); break;
				case 'title':		$content.= form('input td12','title',isset($value['title']) ? $value['title'] : ''); break;
				case 'keywords':	$content.= form('input td12','keywords',isset($value['keywords']) ? $value['keywords'] : ''); break;
				case 'description':	$content.= form('input td12','description',isset($value['description']) ? $value['description'] : ''); break;
			}
		}
		$content.= '</div>';
		return $content;
	}
	elseif ($type=='parent') {
		$cl = explode(' ',$class);
		$cl[1] = isset($cl[1]) ? $cl[1] : 'td4';
		$cl[2] = isset($cl[2]) ? $cl[2] : 'td4';
		$previos = 0;
		$parent_array = $previos_array = array();
		if (isset($_GET['id']) && $_GET['id']=='new') {
			$value['left_key'] = $value['right_key'] = $value['parent'] = 0;
			$value['level'] = 1;
			if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
				$value[$v[0]] = isset($get[$v[0]]) ? $get[$v[0]] : '';
			}
		}
		if (isset($value['left_key'])) {
			//если есть фильтр (например, для языка)
			$where = '';
			if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
				if (isset($value[$v[0]]))
					$where.= " AND ".$v[0]." = '".$value[$v[0]]."'";
			}
			$previos = mysql_select("SELECT id FROM `".$get['m']."` WHERE left_key>".$value['left_key']." AND level=".$value['level']." $where ORDER BY left_key LIMIT 1",'string');
			if ($previos==false) $previos=0;
			$parent_array = "
				SELECT id,name,level,parent
				FROM `".$get['m']."`
				WHERE (left_key<'".$value['left_key']."' OR left_key>'".$value['right_key']."') $where
				ORDER BY left_key
			";
			$previos_array = mysql_select("
				SELECT id,name,level,parent
				FROM `".$get['m']."`
				WHERE parent='".$value['parent']."' AND id!='".$value['id']."' $where
				ORDER BY left_key
			",'array');
			if ($previos_array==false) $previos_array = array();
		}
		$previos_array = array(0=>'В конце списка') + $previos_array;
		$content = form('select '.$cl[1],'nested_sets[parent]',array(isset($value['parent']) ? $value['parent'] : '',$parent_array,'Корень списка'),array('name'=>'Родитель','help'=>'Запись будет находится в корне списка или внутри выбранного элемента'));
		$content.= form('select '.$cl[2],'nested_sets[previous]',array($previos,$previos_array),array('name'=>'Положение внутри родителя перед','help'=>'Запись будет находится в начале списка или перед выбранным элементом'));
		return $content;
	}
	elseif ($type=='CodeMirror') {
		$content = '<div class="codeMirror" style="border-top: 1px solid black; border-bottom: 1px solid black; margin:0 0 5px"><textarea id="codeMirror" name="'.$key.'">';
		$content.= htmlspecialchars($value);
		$content.= '</textarea></div>';
		return $content;
	}
	return '<div class="field '.$class.'">'.$content.'</div>';
}

//загрузка файлов
function form_file ($type,$key,$name,$param = '',$fields = array('name'=>'input','display'=>'checkbox')) {
	global $get,$config,$post;
	$message = '';
	$t = current(explode(' ',$type));
	//обычная загрузка файлов если нет нтмл5
	if ($config['uploader']==0) {
		if ($t=='file') $t = 'mysql';
		if ($t=='file_multi') $t = 'simple';
	}
	//обычная загрузка
	if ($t=='simple') {
		$photos = (isset($post[$key]) && $post[$key]) ? unserialize($post[$key]) : array();
		$content = '
			<div class="files simple" data-i="'.$key.'">
				<div class="name"><span>'.$name.'</span>';
		if (is_array($param)) foreach ($param as $k=>$v) $content.= ' '.$k.' ['.$v.']';
		$content.= '
				</div>
				<input type="file" name="'.$key.'[]" multiple="multiple" title="выбрать файл" />
				<div class="load"></div>
		';

		$content.= '<div class="file"></div>';
		$content.= '<div class="clear"></div>';
		$content.= '<ul class="sortable">';
		$path = 'files/'.$get['m'].'/'.$get['id'].'/'.$key; //папка от корня основной папки
		$root = ROOT_DIR.$path.'/'; //папка от корня сервера
		$n = 0; //порядковый номер в массиве
		//список загруженых файлов
		if ($get['id']!='new' && is_dir($root) && $handle = opendir($root)) {
			while (false !== ($dir = readdir($handle))) {
				if ($dir!= '.' AND $dir!= '..') {
					//удаление масива если нет картинки
					if (!is_dir($root.$dir)) {
						if (isset($photos[$dir])) unset($photos[$dir]);
					}
					//удаление картинки, если нет масива
					elseif (!array_key_exists($dir,$photos)) {
						delete_all($root.$dir,true);
					}
				}
			}
			closedir($handle);
			foreach ($photos as $k=>$v) /*if (is_file($root.$k.'/'.$v['file']))*/ {
				if ($k>$n) $n = $k;
				$img = substr($v['file'],-3)=='pdf' ? '<span class="sprite pdf"></span>' : '<img src="/'.$path.'/'.$k.'/a-'.$v['file'].'" />';
				$content.= '<li data-i="'.$k.'" title="для изменения последовательности картинок переместите блок в нужное место">';
					$content.= '<a onclick="hs.expand(this);return false;" href="/'.$path.'/'.$k.'/'.$v['file'].'" class="img" alt="'.$v['file'].'"><span>'.$img.'</span></a>';
					$content.= '<a href="#" class="sprite delete"></a>';
					$content.= '<input name="'.$key.'['.$k.'][file]" type="hidden" value="'.$v['file'].'" />';
					foreach ($fields as $fname=>$ftype) {
						$title = a18n($fname);
						switch ($ftype) {
							case 'checkbox':
								$checked = (isset($v[$fname]) && $v[$fname]==1) ? ' checked="checked"' : '';
								$content.= '<br /><label><input name="'.$key.'['.$k.']['.$fname.']" type="checkbox" value="1"'.$checked.' /><span>'.$title.'</span></label>';
								break;
							default:
								$content.= '<input class="input" name="'.$key.'['.$k.']['.$fname.']" value="'.@$v[$fname].'" placeholder="'.$n.'" title="'.$title.'" />';
						}
					}
				$content.= '</li>';
			}
		}
		//загрузка файлов
		if ($get['u']=='edit') {
			if (is_dir($root) || mkdir($root,0755,true)) { //создание папки
				$temp = isset($_FILES[$key]['tmp_name']) ? $_FILES[$key]['tmp_name'] : ''; //массив файлов
				if (is_array($temp)) {
					foreach($temp as $k1=>$v1) {
						if (is_uploaded_file($v1)) {//проверка записался ли файл на сервер во временную папку
							$n++;
							$file = strtolower(trunslit($_FILES[$key]['name'][$k1])); //название файла
							include_once (ROOT_DIR.'functions/common_imgs.php');
							//загрузка с параметрами
							if (is_array($param)) {
								$param['a-'] = 'resize 100x100'; //для превью в админке
								foreach ($param as $k=>$v) {
									if ($v) {
										if (is_dir($path.'/'.$n) || mkdir ($path.'/'.$n,0755,true)) {
											$prm = explode(' ',$v);
											img_process($prm[0],$v1,$prm[1],$root.$n.'/'.$k.$file);
											//если есть водяной знак
											if (isset($prm[2])) img_watermark($root.$n.'/'.$k.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$n.'/'.$k.$file,isset($prm[3]) ? $prm[3] : '');
										}
									}
									else copy($temp,$root.$n.'/'.$k.$file);
								}
							//простая загрузка
							} else {
								img_process('resize',$v1,'100x100',$root.$n.'/a-'.$file);	//для превью в админке
								move_uploaded_file($v1,$root.$n.'/'.$file);
							}
							if (is_file($root.$n.'/'.$file)) {
								$photos[$n] = array(
									'file' => $file,
									'name' => current(explode('.',$_FILES[$key]['name'][$k1],2)),
									'display' => 1,
								);
								$content.= '<li data-i="'.$n.'" title="для изменения последовательности картинок переместите блок в нужное место">';
								$content.= '<a onclick="hs.expand(this);return false;" href="/'.$path.'/'.$n.'/'.$file.'" class="img" alt="'.$file.'"><span><img src="/'.$path.'/'.$n.'/a-'.$file.'" /></span></a>';
								$content.= '<a href="#" class="sprite delete"></a>';
								$content.= '<div>'.$file.'</div>';
								$content.= '<input name="'.$key.'['.$n.'][file]" type="hidden" value="'.$photos[$n]['file'].'" />';
								foreach ($fields as $fname=>$ftype) {
									$title = a18n($fname);
									switch ($ftype) {
										case 'checkbox':
											$checked = (isset($photos[$n][$fname]) && $photos[$n][$fname]==1) ? ' checked="checked"' : '';
											$content.= '<br /><label><input name="'.$key.'['.$n.']['.$fname.']" type="checkbox" value="1"'.$checked.' /><span>'.$title.'</span></label>';
											break;
										default:
											$content.= '<input class="input" name="'.$key.'['.$n.']['.$fname.']" value="'.@$photos[$n][$fname].'" placeholder="'.$n.'" title="'.$title.'" />';
									}
								}
								$content.= '</li>';
							}
							else $content.= $file.' ошибка загрузки!<br />';
						}
					}
					mysql_fn('update',$get['m'],array('id'=>$get['id'],$key=>serialize($photos)));
				}
			}
		}
		$content.= '</ul>';
		$content.= '<div class="clear"></div>';
		$content.= '</div>';
	}
	//загрузка с записью в БД
	elseif ($t=='mysql') {
		$file = isset($post[$key]) ? $post[$key] : ''; //название файла
		$path = 'files/'.$get['m'].'/'.$get['id'].'/'.$key; //папка от корня основной папки
		$root = ROOT_DIR.$path.'/'; //папка от корня сервера
		$temp = isset($_FILES[$key]['tmp_name']) ? $_FILES[$key]['tmp_name'] : ''; //error_handler(1,2,3,'-'.serialize($_FILES).'-');
		if ($get['u']=='edit') {
			if (is_uploaded_file($temp)) {//проверка записался ли файл на сервер во временную папку
				if (is_dir($root)) delete_all($root,false);
				if (is_dir($root) || mkdir ($root,0755,true)) { //создание папок для файла
					$file = strtolower(trunslit($_FILES[$key]['name'])); //название файла
					include_once (ROOT_DIR.'functions/common_imgs.php');
					//загрузка с параметрами
					if (is_array($param)) {
						$param['a-'] = 'resize 100x100'; //для превью в админке
						foreach ($param as $k=>$v) {
							if ($v) {
								$prm = explode(' ',$v);
								img_process($prm[0],$temp,$prm[1],$root.$k.$file);
								//если есть водяной знак
								if (isset($prm[2])) img_watermark($root.$k.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$k.$file,isset($prm[3]) ? $prm[3] : '');
							}
							//простое копирование - сохранение оригинальных размеров
							else copy($temp,$root.$k.$file);
						}
					//простая загрузка
					} else {
						img_process('resize',$temp,'100x100',$root.'a-'.$file);	//для превью в админке
						move_uploaded_file($temp,$root.$file);
					}
					//проверка загрузки
					if (is_file($root.$file)) {
						mysql_query("UPDATE ".$get['m']." SET $key = '$file' WHERE id = '".$get['id']."'");
						$message.= ' файл загружен!';
					} else {
						mysql_query("UPDATE ".$get['m']." SET $key = '' WHERE id = '".$get['id']."'");
						$message.= ' ошибка загрузки!';
					}
				} else $message.= ' ошибка создания каталога!';
			}
		}
		$is_file = is_file($root.$file);
		$img = is_file($root.'a-'.$file) ? '/'.$path.'/a-'.$file : '/admin/templates/no_img.png';
		$content = '
			<div class="files mysql" data-i="'.$key.'">
				<div class="data">
					<div class="img" data-img="/'.$path.'/'.$file.'">
						<span>
							<input name="'.$key.'" type="hidden" value="'.$file.'" />
							<img src="'.$img.'" />
						</span>
					</div>
					<div class="name">'.$name.'</div>
					<div class="desc">';
		if ($is_file) {
			$content.= '<a href="#" class="sprite delete"></a>';
			if (is_array($param)) {
				foreach ($param as $k=>$v) if ($k!='a-')
					$content.= '<div><a href="/'.$path.'/'.$k.$file.'" onclick="return hs.expand(this)">'.$k.$file.'</a> ['.$v.']</div>';
			} else {
				$content.= '<div><a href="/'.$path.'/'.$file.'" onclick="return hs.expand(this)">'.$file.'</a></div>';
			}
		}
		if (isset($message) && $message) $content.= '<div class="message">'.$message.'</div>';
		$content.= '
					</div>
					<a class="add_file button green" title="Загрузить файл" style="display:none">
						<span><span class="sprite plus"></span>загрузить</span>
					</a><input type="file" name="'.$key.'" title="выбрать файл" />
					<div class="load"></div>
					<div class="clear"></div>
				</div>
			</div>';
	}
	//загрузка с записью в БД
	elseif ($t=='file') {
		$file = $post[$key] = isset($post[$key]) ? $post[$key] : ''; //название файла
		$path = 'files/'.$get['m'].'/'.$get['id'].'/'.$key; //папка от корня основной папки
		$root = ROOT_DIR.$path.'/'; //папка от корня сервера
		if ($get['u']=='edit') {
			if ($file=='') delete_all($root,true); //удаление картинки
			$temp = ROOT_DIR.'files/temp/'.$file.'/'; //временная папка на сервере
			//если название файла целое число и есть временная папка, значит происходит загрузка нового файла
			if (is_numeric($post[$key]) AND is_dir($temp) AND $handle = opendir($temp)) {
				$temp_file = ''; //название временного файла на сервере
				while (false !== ($f = readdir($handle))) {
					if (strlen($f)>2 && is_file($temp.$f)) {
						$file = strtolower(trunslit($f));
						$temp_file = $temp.$f;
						break;
					}
				}
				if (is_dir($root)) delete_all($root,false); //удаление старого файла
				if ($temp_file && (is_dir($root) || mkdir ($root,0755,true))) { //создание папок для файла
					include_once (ROOT_DIR.'functions/common_imgs.php');
					//загрузка с параметрами
					if (is_array($param)) {
						$param['a-'] = 'resize 100x100'; //для превью в админке
						foreach ($param as $k=>$v) {
							if ($v) {
								$prm = explode(' ',$v);
								img_process($prm[0],$temp_file,$prm[1],$root.$k.$file);
								//если есть водяной знак
								if (isset($prm[2])) img_watermark($root.$k.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$k.$file,isset($prm[3]) ? $prm[3] : '');
							}
							//простое копирование - сохранение оригинальных размеров
							else copy($temp_file,$root.$k.$file);
						}
					//простая загрузка
					} else {
						img_process('resize',$temp_file,'100x100',$root.'a-'.$file);	//для превью в админке
						copy($temp_file,$root.$file);
					}
					delete_all($temp,true);
					//проверка загрузки
					if (is_file($root.$file)) {
						mysql_query("UPDATE ".$get['m']." SET $key = '$file' WHERE id = '".$get['id']."'");
						$post[$key] = $file;
						//$message.= ' файл загружен!';
					} else {
						mysql_query("UPDATE ".$get['m']." SET $key = '' WHERE id = '".$get['id']."'");
						$post[$key] = '';
						//$message.= ' ошибка загрузки!';
					}
				} //else $message.= ' ошибка создания каталога!';
			}
		}
		$is_file = is_file($root.$file);
		$img = is_file($root.'a-'.$file) ? '/'.$path.'/a-'.$file : '/admin/templates/no_img.png?2';

		$content = '
			<div class="files '.$type.'" data-i="'.$key.'">
				<div class="data">
					<div class="img" data-img="/'.$path.'/'.$file.'" title="Для загрузки картинки переместите её в эту область"><img src="'.$img.'" /><span>&nbsp;</span><input name="'.$key.'" type="hidden" value="'.$file.'" /></div>
					<div class="name">'.$name.'</div>
					<div class="desc">';
		if ($is_file) {
			$content.= '<a href="#" class="sprite delete"></a>';
			if (is_array($param)) {
				foreach ($param as $k=>$v) {
					$content.= ($k!='a-') ? '<div><a href="/'.$path.'/'.$k.$file.'" onclick="return hs.expand(this)">'.a18n('img'.$k).'</a> <span>'.$v.'</span></div>' : '';
				}
			} else {
				$content.= '<div><a href="/'.$path.'/'.$file.'" onclick="return hs.expand(this)">'.a18n('img').'</a></div>';
			}
		} else {
			if (is_array($param)) foreach ($param as $k=>$v)  if ($k!='a-') $content.= '<div>'.a18n('img'.$k).' <span>'.$v.'</span></div>';
		}
		$content.= '
					</div>
					<a class="add_file button green" title="Выбрать файл">
						<span><span class="sprite plus"></span>выбрать</span>
						<input type="file" title="выбрать файл" />
					</a>
					<div class="clear"></div>
				</div>
			</div>';
	}
	//обычная загрузка
	if ($t=='file_multi') {
		//error_handler(1,serialize($_FILES),1,1);
		$photos = (isset($post[$key]) && $post[$key]) ? unserialize($post[$key]) : array();
		$content = '
			<div class="files '.$type.'" data-i="'.$key.'">
				<div class="data">
					<div class="img" title="Для загрузки картинки переместите её в эту область"><img src="/admin/templates/no_img.png?2" /></div>
					<div class="name">'.$name.'</div>
					<div class="desc">';
			if (is_array($param)) foreach ($param as $k=>$v) $content.= '<div>'.a18n('img'.$k).' <span>'.$v.'</span></div>';
			$content.= '
					</div>
					<a class="add_file button green" title="Выбрать файлы">
						<span><span class="sprite plus"></span>выбрать</span>
						<input type="file" multiple="multiple" title="выбрать файл" />
					</a>
					<div class="clear"></div>
				</div>
				<ul class="sortable">';
		$path = 'files/'.$get['m'].'/'.$get['id'].'/'.$key; //папка от корня основной папки
		$root = ROOT_DIR.$path.'/'; //папка от корня сервера
		//загрузка файлов
		if ($get['u']=='edit' AND $photos) {
			if ($photos) {
				$update = 0;
				if (is_dir($root) || mkdir($root,0755,true)) { //создание папки
					foreach ($photos as $n=>$val) {
						$temp = ROOT_DIR.'files/temp/'.@$val['temp'].'/';
						//если есть временная папка, то копируем картинку
						if (@$val['temp'] AND $handle = opendir($temp)) {
							$update++;
							$temp_file = ''; //название временного файла на сервере
							while (false !== ($f = readdir($handle))) {
								if (strlen($f)>2 && is_file($temp.$f)) {
									$file = strtolower(trunslit($f));
									$temp_file = $temp.$f;
									break;
								}
							}
							if (is_dir($root.$n)) delete_all($root.$n,false); //удаление старого файла
							if ($temp_file AND (is_dir($root.$n) || mkdir($root.$n,0755,true))) {
								include_once (ROOT_DIR.'functions/common_imgs.php');
								//загрузка с параметрами
								if (is_array($param)) {
									$param['a-'] = 'resize 100x100'; //для превью в админке
									foreach ($param as $k=>$v) {
										if ($v) {
											$prm = explode(' ',$v);
											img_process($prm[0],$temp_file,$prm[1],$root.$n.'/'.$k.$file);
											//если есть водяной знак
											if (isset($prm[2])) img_watermark($root.$n.'/'.$k.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$n.'/'.$k.$file,isset($prm[3]) ? $prm[3] : '');
										}
										//простое копирование - сохранение оригинальных размеров
										else copy($temp_file,$root.$n.'/'.$k.$file);
									}
								}
								//простая загрузка
								else {
									img_process('resize',$temp_file,'100x100',$root.$n.'/a-'.$file);	//для превью в админке
									copy($temp_file,$root.$n.'/'.$file);
								}
								$photos[$n]['file'] = $file;
								unset($photos[$n]['temp']);
							}
							else unset($photos[$n]);
							//удаляем временную папку
							delete_all($temp,true);
						}
					}
				}
				if ($update>0) mysql_fn('update',$get['m'],array('id'=>$get['id'],$key=>$photos ? serialize($photos) : ''));
			}
		}
		//список загруженых файлов
		if ($get['id']!='new' && is_dir($root)) {
			if ($handle = opendir($root)) {
				while (false !== ($dir = readdir($handle))) {
					if ($dir!= '.' AND $dir!= '..') {
						//удаление масива если нет картинки
						if (!is_dir($root.$dir)) {
							if (isset($photos[$dir])) unset($photos[$dir]);
						}
						//удаление картинки, если нет масива
						elseif (!array_key_exists($dir,$photos)) {
							delete_all($root.$dir,true);
						}
					}
				}


				closedir($handle);
			}
			foreach ($photos as $k=>$v) {
				if (@$v['file'] AND is_file($root.$k.'/'.$v['file'])) {
					if (is_file($root.$k.'/a-'.$v['file'])) $img = '<img src="/'.$path.'/'.$k.'/a-'.$v['file'].'" />';
					else {
						$exc = end(explode('.',$v['file']));
						$icon = '/admin/templates/icons/blank.png';
						if (in_array($exc,array('sql','txt','doc','docx')))	$icon = '/admin/templates/icons/doc.png';
						elseif (in_array($exc,array('xls','xlsx')))		$icon = '/admin/templates/icons/xls.png';
						elseif (in_array($exc,array('pdf')))			$icon = '/admin/templates/icons/pdf.png';
						elseif (in_array($exc,array('zip','rar')))		$icon = '/admin/templates/icons/zip.png';
						$img = '<img src="'.$icon.'" />';
					}
					$content.= '<li data-i="'.$k.'" title="для изменения последовательности картинок переместите блок в нужное место">';
						$content.= '<div class="img"><span>&nbsp;</span>'.$img;
						$content.= '<input name="'.$key.'['.$k.'][temp]" type="hidden" value="" />';
						$content.= '<input name="'.$key.'['.$k.'][file]" value="'.$v['file'].'" /></div>';
						$content.= '<input type="file" name="'.$key.'['.$k.'][file]" />';
						$content.= '<a href="#" class="sprite delete"></a>';
						$content.= '<div>'.$v['file'].'</div>';
						foreach ($fields as $fname=>$ftype) {
							$n = a18n($fname);
							switch ($ftype) {
								case 'checkbox':
									$checked = (isset($v[$fname]) && $v[$fname]==1) ? ' checked="checked"' : '';
									$content.= '<br /><label><input name="'.$key.'['.$k.']['.$fname.']" type="checkbox" value="1"'.$checked.' /><span>'.$n.'</span></label>';
									break;
								default:
									$content.= '<input class="input" name="'.$key.'['.$k.']['.$fname.']" value="'.@$v[$fname].'" placeholder="'.$n.'" title="'.$n.'" />';
							}
						}
					$content.= '</li>';
				}
			}
		}
		$content.= '</ul>';
		$content.= '<div class="clear"></div>';
		$content.= '</div>';
	}
	return $content;
}

//верхнее меню модулей
function head ($modules,$m='') {
	global $user;
	$top=$bottom='';
	$parent = $child = 0;
	$modules = array_merge_recursive(array('<span class="sprite home"></span>'=>'index'),$modules);
	foreach ($modules as $key => $value) {
		if (is_array($value)) {
			$i=0;
			if (in_array($m, $value)) {
				foreach ($value as $k=>$v) {
					if (access('admin module',$v)) {
						$parent++;
						$child++;
						$i++;
						if ($i==1) $top.='<a href="/admin.php?m='.$v.'" class="a">'.a18n($key).'</a>';
						$link = $m==$v ? ' class="a"' : '';
						$bottom.='<a href="/admin.php?m='.$v.'"'.$link.'>'.a18n($k).'</a>';
					}
				}
			}
			else {
				foreach ($value as $k=>$v) {
					if (access('admin module',$v)) {
						$parent++;
						$top.='<a href="/admin.php?m='.$v.'">'.a18n($key).'</a> ';
						break;
					}
				}
			}
		}
		elseif (access('admin module',$value)) {
			$link = $m==$value ? ' class="a"' : '';
			$top.='<a href="/admin.php?m='.$value.'"'.$link.'>'.a18n($key).'</a>';
		}
	}
	if ($parent>1)
		return '<div class="menu_parent gradient">'.$top.'</div>'.(($bottom AND $child>1) ? '<div class="menu_child corner_bottom">'.$bottom.'</div>' : '');
}

//дерево вложенности
function nested_sets($m,$id,$selected,$insert,$filter=array()) {
	//принимающий
	$id = mysql_select("
		SELECT *
		FROM ".$m."
		WHERE id = '".intval($id)."'
	",'row');
	//перемещаемый
	$selected = mysql_select("
		SELECT *
		FROM ".$m."
		WHERE id = '".intval($selected)."'
	",'row');
	if ((!is_array($id) AND $insert=='prev') OR !is_array($selected)) return 'нет записи!';
	//если дерево многослойное и есть фильтр
	$where = '';
	if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
		if (is_array($id) AND $id[$v[0]]!=$selected[$v[0]]) return 'несовместимость';
		if (is_array($id)) $where.= " AND `".$v[0]."` = ".$id[$v[0]];
	}
	//количество переносимых записей * 2
	$dbl_count = $selected['right_key'] - $selected['left_key'] + 1;
	//имитация удаления узла - level делаем минусовым для отличия
	$query = "
		UPDATE ".$m."
		SET level = (0 - level),
			left_key = (left_key - ".$selected['left_key']." + 1),
			right_key = (right_key - ".$selected['left_key']." + 1)
		WHERE left_key>=".$selected['left_key']."
			AND right_key<=".$selected['right_key'].
			$where
	; //echo $query.'<br />';
	mysql_query($query);
	//пересортировка после псевдоудаления для всеx у кого level>0 (те что level<0 считаются удаленными)
 	$query = "
 		UPDATE ".$m."
		SET left_key = CASE WHEN left_key > ".$selected['left_key']."
							THEN left_key - ".$dbl_count."
							ELSE left_key END,
			right_key = right_key - ".$dbl_count."
		WHERE right_key > ".$selected['right_key']."
			AND level > 0".
			$where
	; //echo $query.'<br />';
	mysql_query($query);

	//обновляем принимающий, т.к. была произведена пересортировка шагом ранее
	if (is_array($id))
		$id = mysql_select("
			SELECT *
			FROM ".$m."
			WHERE id = '".$id['id']."'
		",'row');
	else
		$id = array(
			'id'=>0,
			'right_key'=> intval(mysql_select("SELECT IFNULL(MAX(right_key),0) FROM ".$m." WHERE ".$m.".level>0 ".$where,'string'))+1,
			'level'=>0
		);
	//вставка в конец узла ======================
	if ($insert=='parent') {
		//подготовка для создания создания нового узла
		//пересортировка - освобождение места для нового узла
		if ($id['id']>0) {
			$query = "
				UPDATE ".$m."
				SET right_key = right_key + ".$dbl_count.",
					left_key = CASE WHEN left_key > ".$id['right_key']."
									THEN left_key + ".$dbl_count."
									ELSE left_key END
				WHERE right_key >= ".$id['right_key']."
					AND level > 0".
					$where
			; //echo $query.'<br />';
			mysql_query($query);
		}
		//имитация создания нового узла
		$shift = $id['right_key'] - 1;
		$level = $id['level'] + 1 - $selected['level'];
		$query = "
			UPDATE ".$m."
			SET level = (0 - level + ".$level."),
				left_key = (left_key + ".$shift."),
				right_key = (right_key + ".$shift.")
			WHERE level < 0".
				$where
		; //echo $query.'<br />';
		mysql_query($query);
		//обновление родителя
		$query = "
			UPDATE ".$m."
			SET parent = ".$id['id']."
			WHERE id = ".$selected['id'].
				$where
		; //echo $query.'<br />';
		mysql_query($query);
	//вставка перед узлом ======================
	} elseif ($insert=='prev') {
		//подготовка для создания создания нового узла
		//пересортировка - освобождение места для нового узла
		mysql_query("
			UPDATE ".$m."
			SET right_key = right_key + ".$dbl_count.",
				left_key = CASE WHEN left_key >= ".$id['left_key']."
							THEN left_key + ".$dbl_count."
							ELSE left_key END
			WHERE right_key > ".$id['left_key']."
				AND level > 0".
				$where
		);
		//имитация создания нового узла
		$shift = $id['left_key'] - 1;
		$level = $id['level'] - $selected['level'];
		mysql_query("
			UPDATE ".$m."
			SET level = (0 - level + ".$level."),
				left_key = (left_key + ".$shift."),
				right_key = (right_key + ".$shift.")
			WHERE level < 0".
				$where
		);
		//обновление родителя
		mysql_query("
			UPDATE ".$m."
			SET parent = ".$id['parent']."
			WHERE id = ".$selected['id'].
				$where
		);
	}
	//проверка
	$where = '';
	if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
		if(isset($id[$v[0]])) $where.= " AND t1.".$v[0]." = ".$id[$v[0]]." AND t2.".$v[0]." = ".$id[$v[0]]."";
	}
	$result = mysql_query("
		SELECT t1.*,t2.*
		FROM ".$m." AS t1, ".$m." AS t2
		WHERE (t1.left_key = t2.left_key OR t1.right_key = t2.right_key)
			AND t1.id!=t2.id ".
			$where
	); echo mysql_error();
	if (mysql_num_rows($result) > 0) return 'ошибка!';
	return true;
}

?>