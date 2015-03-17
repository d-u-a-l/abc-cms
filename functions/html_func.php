<?php

/**
 * нтмл код селекта
 * @param $key - ключ либо массив с ключами для селекта
 * @param $query - 1)массив option; 2)модуль или запрос
 * @param $default	- значени по умолчанию
 * @param $template - шаблон
 * @return html код селекта
 */
function select($key,$query,$default = NULL,$template = '{name}') {
	if (isset($default)) $content = $default ? '<option value="">'.$default.'</option>' : '<option value="">'.i18n('common|make_selection').'</option>';
	else $content = '';
	//1)список из массива - $query - массив со значениями для селектов
	if (is_array($query)) foreach ($query as $k=>$v) {
		if (is_array($v) && !is_int($k)) {
			$content.= '<optgroup label="'.$k.'">';
			$content.= select($key,$v,$default,$template);
			$content.= '</optgroup>';
		}
		else {
			if (is_array($key)) $selected = in_array($k, $key) ? 'selected="selected"' : '';	//для multiple
			else $selected = ($k==$key AND (string)$key!='') ? 'selected="selected"' : '';		//для select
			$content.= '<option value="'.htmlspecialchars($k).'" '.$selected.'>'.(is_array($v) ? $v['name'] : $v).'</option>';
		}
	}
	//2)список из sql-запроса - $query - sql-запрос или таблица
	else {
		//если нет пробела сформировать запрос
		if (!strpos($query, ' ')) $query = "SELECT id,name,level FROM `".$query."` ORDER BY left_key";
		$result = mysql_query($query);
		if ($error = mysql_error()) trigger_error($error.' '.$query, E_USER_DEPRECATED);
		while ($q = mysql_fetch_assoc ($result)) {
			$nbsp = '';
			if (isset($q['level'])) {
				for ($i = 1; $i<$q['level']; $i++) $nbsp.= '&nbsp; ';
				$nbsp.= ':.. ';
			}
			if (is_array($key)) $selected = in_array($q['id'], $key) ? 'selected="selected"' : '';	//для multiple
			else $selected = $q['id']==$key ? 'selected="selected"' : '';							//для select
			if (isset($q['parent'])) $selected.=' data-parent="'.$q['parent'].'"';
			$str = $template;
			foreach ($q as $k=>$v) $str = str_replace ("{".$k."}", $q[$k], $str);
			$content.= '<option value="'.$q['id'].'" '.$selected.'>'.$nbsp.$str.'</option>';

		}
	}
	return $content;
}


/**
 * шаблонизатор - заменяет {i} на $data['i']
 * @param $template - строка с {i}
 * @param $data - массив со значениями для замены
 * @return - строка $template с заменой
 */
function template($template,$data) {
	preg_match_all('/{(.*?)}/',$template,$matches,PREG_PATTERN_ORDER);
	foreach($matches[1] as $k=>$v) {
		//получаем массив ключей
		$keys = explode('|',$v);
		//создаем вспомогательную переменную, потому как массив $data трогать нельзя. В принципе, можно использовать $matches[1][$k], но лучше перестраховаться
		$replacement = $data;
		//последовательно движемся по всей цепочке ключей.
		//если ключ один (например, {name}), то и foreach выполнит только один цикл, что дает фактически ту же функцию без многомерности
		//если ключей много, то каждый раз проверяем, существует ли такой ключ - если нет, сохраняем пустую строку и выходим из цикла, если есть, цикл продолжаем, сохраняя значение из массива, соотв. ключу
		foreach ($keys as $i=>$key) {
			if (isset($replacement[$key])) { //можно, в принципе, написать array_key_exists($key,$replacement), это уже нюансы, думаю, что isset правильнее
				$replacement = $replacement[$key];
			} else {
				$replacement = '';
				break;
			}
		}
		$matches[1][$k] = is_array($replacement) ? '' : $replacement;
	}
	return str_replace($matches[0],$matches[1],$template);
}

//замена {page/text} на шаблон
function html_template($text) {
	preg_match_all('/{(.*?)}/',$text,$matches,PREG_PATTERN_ORDER);
	foreach($matches[1] as $k=>$v) {
		$matches[1][$k] = is_file(ROOT_DIR.'templates/includes/'.$v.'.php') ? html_array($v) : '';
	}
	return str_replace($matches[0],$matches[1],$text);
}

//наполнение шаблона значенями массива
function html_array($m,$q = '',$k = '') {
	global $config,$modules,$u,$user,$lang,$page;
	$i = $num_rows = 1;
	ob_start(); // echo to buffer, not screen
	include (ROOT_DIR.$config['style'].'/includes/'.$m.'.php');
	return ob_get_clean(); // get buffer contents
}

//наполнение шаблона выборкой с БД
function html_query($module, $query, $no_results = false, $cache = false, $cache_type = 'html') {
	//$module - путь к файлу шаблона, через пробел путь к файлу пагинатора
	//$query - sql запрос
	//$no_results - строка в случае если нет результатов запроса
	//$cache - время обновления кеша, если пусто то кеш не создается
	//$cache_type - вид кеша - нтмл файл или массив json
	global $config,$lang,$modules,$user,$u,$page;
	$content	= false;
	$data		= array();
	$m			= explode(' ',$module);
	$time		= time() - $cache;
	//если есть пагинатор подключить его, в нем к $query прибавляется LIMIT n,c
	if (isset($m[1]) && file_exists(ROOT_DIR.$config['style'].'/includes/pagination/'.$m[1].'.php')) include (ROOT_DIR.$config['style'].'/includes/pagination/'.$m[1].'.php');
	//если есть кеширование и входной параметр $query - строка
	if ($config['cache'] && $cache && is_string($query)) {
		if ($cache_type=='json') $file	= md5($query).'.php';
		else $file	= md5($query).'.html';
		$config['queries'][$file] = $query;
		$file = ROOT_DIR.'cache/'.$file;
		if (file_exists($file) && $time<filemtime($file)) {
			if ($cache_type=='json') {
				$content = '';
				$data = json_decode(file_get_contents($file),true);
				if (is_array($data)) {
					$num_rows = count($data);
					$i = 1;
					foreach ($data as $q) {
						ob_start(); // echo to buffer, not screen
						include (ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php');
						$content.= ob_get_clean(); // get buffer contents
						$i++;
					}
				}
			}
			else $content.= file_get_contents($file);
		}
	}
	//если нет результата кеширования то подключаем шаблон и делаем запрос в БД
	if ($content===false && file_exists(ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php'))  {
		//если в качестве второго параметра задан массив
		if (is_array($query)) {
			if ($num_rows = count($query)) {
				$i = 1;
				foreach ($query as $k=>$q) {
					ob_start(); // echo to buffer, not screen
					include (ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php');
					$content.= ob_get_clean(); // get buffer contents
					$data[] = $q;
					$i++;
				}
			}
		//если в качестве второго параметра задан запрос к БД
		} else {
			if ($config['mysql_connect']==false) {
				mysql_connect_db();
			}
			if ($config['mysql_error']==false) {
				$config['queries'][] = $query;
				$result = mysql_query($query); if ($i = mysql_error()) echo '<br />'.$i.'<br />'.$query;
				if ($num_rows = mysql_num_rows($result)){
					$i = 1;
					while ($q = mysql_fetch_assoc($result)) {
						ob_start(); // echo to buffer, not screen
						include (ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php');
						$content.= ob_get_clean(); // get buffer contents
						$data[] = $q;
						$i++;
					}
				}
				//создаем файл кеша
				if ($config['cache'] && $cache && (is_dir(ROOT_DIR.'cache') || mkdir(ROOT_DIR.'cache',0755,true))) {
					$f = fopen($file,'w');
					if ($cache_type=='json') fwrite($f,json_encode($data));
					else fwrite($f,$content);
					fclose($f);
				}
			}
		}
	}
	//если нет результатов
	if ($content=='') {
		if ($no_results===false) {
			$no_results = i18n('common|msg_no_results');
		}
		$content = $no_results ? '<div class="no_results">'.$no_results.'</div>' : '';
	}
	//подлючаем пагинатор
	if (isset($pagination)) $content = str_replace('{content}',$content,$pagination);
	return $content;
}

//функция формирует бредкрамб
function breadcrumb($query,$template = '/{url}/',$cache = false) {
	$data = mysql_select($query,'rows',$cache);
	if (is_array($data)) {
		foreach ($data as $key=>$value) {
			$str = $template;
			foreach ($value as $k=>$v) $str = str_replace ("{".$k."}", $value[$k], $str);
			$breadcrumb[] = array($value['name'],$str);
		}
		return $breadcrumb;
	}
}

//возвращает атрибуты для редактируемого блока
function editable($edit,$editable='editable_str',$class='') {
	global $lang;
	$array = explode('|',$edit);
	if (access('editable '.$array[0]) && !isset($_GET['i18n'])) return ' class="'.$editable.($class ? ' '.$class : '').'" data-edit="'.$lang['id'].'|'.$edit.'"';
	return $class ? ' class="'.$class.'"' : '';
}

function html_sources($label='',$source='') {
	global $config,$lang;
	$data = array(
	'css_reset'=>
'<link href="/'.$config['style'].'/css/reset.css" rel="stylesheet" type="text/css" />',
	'css_common'=>
'<link href="/'.$config['style'].'/css/common.css?'.filemtime(ROOT_DIR.$config['style'].'/css/common.css').'" rel="stylesheet" type="text/css" />',
	'script_common'=>
'<script type="text/javascript" src="/'.$config['style'].'/scripts/common.js?'.filemtime(ROOT_DIR.$config['style'].'/scripts/common.js').'"></script>',
	'jquery'				=> '
<script type="text/javascript" src="/plugins/jquery/jquery-1.11.1.min.js"></script>',
	'jquery_cookie'			=> '
<script type="text/javascript" src="/plugins/jquery/jquery.cookie.js"></script>',
	'jquery_ui'				=> '
<script type="text/javascript" src="/plugins/jquery/jquery-ui-1.11.1.custom.min.js"></script>',
	'jquery_ui_style'		=> '
<link rel="stylesheet" type="text/css" href="/plugins/jquery/redmond/jquery-ui-1.8.17.custom.css" />',
	'jquery_localization' 	=> '
<script type="text/javascript" src="/plugins/jquery/i18n/jquery.ui.datepicker-'.$lang['localization'].'.js"></script>',
	'jquery_form' 			=> '
<script type="text/javascript" src="/plugins/jquery/jquery.form.min.js"></script>',
	'jquery_uploader' 		=> '
<script type="text/javascript" src="/plugins/jquery/jquery.uploader.js"></script>',
	'jquery_validate'		=> '
<script type="text/javascript" src="/plugins/jquery/jquery-validation-1.8.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="/plugins/jquery/jquery-validation-1.8.1/additional-methods.min.js"></script>
<script type="text/javascript" src="/plugins/jquery/jquery-validation-1.8.1/localization/messages_'.$lang['localization'].'.js"></script>',
	'jquery_multidatespicker' => '
<script type="text/javascript" src="/plugins/jquery/jquery-ui.multidatespicker.js"></script>',
	'highslide'				=>	'
<script type="text/javascript" src="/plugins/highslide/highslide.packed.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/highslide/highslide.css" />
<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="/plugins/highslide/highslide-ie6.css" /><![endif]-->',
	'highslide_gallery'	=>	'
<script type="text/javascript" src="/plugins/highslide/highslide-with-gallery.js"></script>
<script type="text/javascript" src="/'.$config['style'].'/scripts/highslide.js?1" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/plugins/highslide/highslide.css?1" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="/plugins/highslide/highslide-ie6.css" />
<![endif]-->',
	'tinymce'=>'<script type="text/javascript" src="/plugins/tinymce/tinymce.min.js?3"></script>',
	'editable'=>'<script type="text/javascript" src="/templates/scripts/editable.js?1"></script>',
	);
	$content = '';
	$sources = explode(' ',$source);
	foreach ($sources as $k=>$v) {
		//если есть такой ресурс
		if (isset($data[$v])) {
			//если первый раз подключается то записываем в $content
			if (!isset($config['sources'][$v])) {
				$config['sources'][$v] = $data[$v];
				if ($label=='return') $content.= $data[$v];
			}
		}
	}
	if ($label=='return') return $content;
}
?>