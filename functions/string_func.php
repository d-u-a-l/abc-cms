<?php

//генерация ключевых слов
function keywords($str) { //04.02.10 поиск ключевых слов в тексте
	$keywords = '';
	if (strlen($str)>0) {
		$str = preg_replace("/&[\w]+;/", ' ',$str);	//замена символов типа &nbsp; на пробел
		$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
		$str = preg_replace('~[^-їієа-яa-z0-9 ]+~u', ' ', $str);
		$token = strtok($str, ' ');
		$array = array();
		while ($token) {
			$token = trim($token);
			if (strlen($token)>=4) {
				if (!isset($array[$token])) $array[$token]=0;
				$array[$token]++;
			}
			$token = strtok(' ');
		}
		if (count($array)>0) {
			arsort ($array);
			foreach ($array as $key=>$value) {
				if (strlen($keywords.', '.$key)>255) break;
				$keywords.= ', '.$key;
			}
			return substr($keywords, 2);
		}
	}
}

//генерирует описание из текста
function description($str) {
	$description = '';
	$str = preg_replace("/&[\w]+;/", ' ',$str);	//замена символов типа &nbsp; на пробел
	$str = trim(strip_tags($str));
	$token = strtok($str, ' ');
	while ($token) {
		$token = trim($token);
		if ($token!='') {
			if (strlen($description.' '.$token)>255) break;
			$description.= trim($token).' ';
		}
		$token = strtok(' ');
	}
	return trim($description);
}

//преобразование кирилицы в транслит
function trunslit($str){
	$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
	$str = str_replace(
		array('a','o','u','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я','і','ї','є'),
		array('a','o','u','a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','shch','','y','','e','yu','ya','i','yi','e'),
		$str
	);
	$str = preg_replace('~[^-a-z0-9_.]+~u', '-', $str);	//удаление лишних символов
	$str = preg_replace('~[-]+~u','-',$str);			//удаление лишних -
	$str = trim($str,'-');								//обрезка по краям -
	$str = trim($str,'.');
	return $str;
}

//зaмена функции strtolower
if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str,$enc = 'UTF-8') {
		$large = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','A','A','A','A','A','A','?','C','E','E','E','E','I','I','I','I','?','N','O','O','O','O','O','O','U','U','U','U','Y','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','Є');
		$small = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','a','a','a','a','a','a','?','c','e','e','e','e','i','i','i','i','?','n','o','o','o','o','o','o','u','u','u','u','y','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','є');
		return str_replace($large,$small,$str);
	}
}
//оставлена для совместимости
function strtolower_utf8($str){
	return mb_strtolower($str,'UTF-8');
}

//корректировка часового пояса
function time_zone($date,$time_zone = false) {
	if ($time_zone==false) {
		if (access('user auth')==false) return $date;
		global $user;
		$time_zone = $user['time_zone'];
	}
	if ($time_zone==4) return $date;
	return strftime('%Y-%m-%d %H:%M:%S',(strtotime($date)+($time_zone-4)*60*60));
}

//доставляет нули к числу
function zerofill($number,$n = 7) {
	return str_pad($number,$n,'0',STR_PAD_LEFT);
}

//конвертация даты
function date2($date,$type) {
	$months = array(
		'01'	=>	'января',
		'02'	=>	'февраля',
		'03'	=>	'марта',
		'04'	=>	'апреля',
		'05'	=>	'мая',
		'06'	=>	'июня',
		'07'	=>	'июля',
		'08'	=>	'августа',
		'09'	=>	'сентября',
		'10'	=>	'октября',
		'11'	=>	'ноября',
		'12'	=>	'декабря',
	);
	if ($type=='d month y') {
		if (is_string($date)) $date = strtotime($date);
		$d = strftime('%d',$date);
		$m = strftime('%m',$date);
		$y = strftime('%Y',$date);
		return $d.' '.$months[$m].' '.$y;
	}
	else return strftime($type,strtotime($date));
}

//обрезание текста
function about($text,$lenght = 1000,$strip_tags = '<br><img>',$end = '..') {
	$text  = strip_tags($text,$strip_tags);
	if (strlen($text)>$lenght) {
		$text = iconv_substr($text,0,$lenght,"UTF-8");
		$text.= $end;
	}
	return $text;
}
?>