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
 * @param string $template - строка с {i}
 * @param array $data - массив со значениями для замены
 * @return - строка $template с заменой
 * todo
 * закинуть html_template сюдаже
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


/**
 * замена {page/text} на шаблон
 * @param string $text - html код с тектсом
 * @return string - html код с тектсом c подключенным шаблоном
 */
function html_template($text) {
	preg_match_all('/{(.*?)}/',$text,$matches,PREG_PATTERN_ORDER);
	foreach($matches[1] as $k=>$v) {
		$matches[1][$k] = is_file(ROOT_DIR.'templates/includes/'.$v.'.php') ? html_array($v) : '';
	}
	return str_replace($matches[0],$matches[1],$text);
}

/**
 * //подключение шаблона - наполнение шаблона значенями массива
 * @param string $path - путь к шаблону
 * @param string|array $q - массив данных или строка
 * @return string
 */
function html_array($path,$q = array()) {
	global $config,$modules,$u,$user,$lang,$page;
	$i = $num_rows = 0;
	ob_start(); // echo to buffer, not screen
	include (ROOT_DIR.$config['style'].'/includes/'.$path.'.php');
	return ob_get_clean(); // get buffer contents
}

/**
 * наполнение шаблона выборкой с БД
 * @param string $path - путь к файлу шаблона, через пробел путь к файлу пагинатора
 * @param $query - sql запрос
 * @param bool|string $no_results - строка в случае если нет результатов запроса, если false то фраза по умолчанию  i18n('common|msg_no_results')
 * @param bool $cache - время обновления кеша в секундах, если пусто то кеш не создается
 * @param string $cache_type - html - генерируется нтмл файл, json - json массив в файле
 * @return bool|string
 */
function html_query($path, $query, $no_results = false, $cache = 0, $cache_type = 'html') {
	global $config,$lang,$modules,$user,$u,$page;
	$content	= false;
	$data		= array();
	$m			= explode(' ',$path);
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
			if (mysql_connect_db()) {
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

/**
 * функция формирует бредкрамб
 * @param string $query - SQL запрос
 * @param string $template - шаблон урл, например, /shop/{id}-{url}/
 * @param int $cache - время кеширования в секундах
 * @return array
 */
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


/**
 * возвращает атрибуты для редактируемого блока
 * @param string $edit -
 * @param string $editable - тип редактора [editable_str,editable_text]
 * @param string $class - значение атрибута класс
 * @return string - атрибут класс
 * todo
 * можно не извращаться с класами и сделать на дата атрибутах
 */
function editable($edit,$editable='editable_str',$class='') {
	global $lang;
	$array = explode('|',$edit);
	if (access('editable '.$array[0]) && !isset($_GET['i18n'])) return ' class="'.$editable.($class ? ' '.$class : '').'" data-edit="'.$lang['id'].'|'.$edit.'"';
	return $class ? ' class="'.$class.'"' : '';
}

/**
 * подключение скриптов
 * @param string $label - метка - можем сразу выводить скрипт (return), а можем собирать в метку для последующего вывода (head|footer)
 * @param string $source - названия скриптов через пробел, описаны в $config['sources'] в _config2.php
 * @return string
 * $config['html_sources'] - глобальная перепенная для определения подключенных файлов
 */
function html_sources($label='',$source='') {
	global $config, $lang;
	$content = array();
	if ($source) {
		$sources = explode(' ', $source);
		foreach ($sources as $k=>$v) {
			//если есть такой ресурс
			if (isset($config['sources'][$v])) {
				//если первый раз подключается то записываем в массив $config['sources']
				if (!isset($config['html_sources'][$v])) {
					$config['html_sources'][$v] = $config['sources'][$v];
					if ($label == 'return') $content[] = $config['sources'][$v];
				}
			}
			else {
				trigger_error('не подключен скрипт в $config[\'sources\'] '.$v,E_USER_DEPRECATED);
			}
		}
	}
	//если $sourсe не указано то выводим метку
	else {
		$content = isset($config['html_sources'][$label]) ? $config['html_sources'][$label] : array();
	}
	//если возвращаем результат то компилируем код
	if (count($content)>0) {
		$text = '';
		foreach ($content as $key=>$val) {
			if (is_array($val)) {
				foreach ($val as $k=>$v) {
					//заменяем {localization} на метку языка
					$str = template($v, $lang);
					if (file_exists(ROOT_DIR . trim($str, '?'))) {
						//если есть знак вопроса то добавляем временную метку
						$str .= substr($v, -1) == '?' ? filemtime(ROOT_DIR.trim($v,'?')) : '';
						//js или css
						$text .= strpos($v, '.js') ? '<script type="text/javascript" src="' . $str . '"></script>' : '<link href="' . $str . '" rel="stylesheet" type="text/css" />';
					}
					else {
						trigger_error('нет файла '.$v,E_USER_DEPRECATED);
					}
				}
			}
			else {
				//заменяем {localization} на метку языка
				$str = template($val, $lang);
				if (file_exists(ROOT_DIR.trim($str,'?'))) {
					//если есть знак вопроса то добавляем временную метку
					$str .= substr($val, -1) == '?' ? filemtime(ROOT_DIR.trim($val,'?')) : '';
					//js или css
					$text .= strpos($val, '.js') ? '<script type="text/javascript" src="' . $str . '"></script>' : '<link href="' . $str . '" rel="stylesheet" type="text/css" />';
				}
				else {
					trigger_error('нет файла '.$val,E_USER_DEPRECATED);
				}
			}
		}
		return $text;
	}
}

?>