<?php

//создание массива $lang
function lang($id,$type='id') {
	switch ($type) {
		case 'id':	$where = "id = ".intval($id);	break;
		case 'url':	$where = "url = '".mysql_real_escape_string($id)."'";
	}
	if (isset($where))
		$lang = mysql_select("SELECT * FROM languages WHERE ".$where." LIMIT 1",'row',60*60);
	if ($lang==false)
		$lang = mysql_select("SELECT * FROM languages WHERE display=1 ORDER BY rank DESC LIMIT 1",'row',60*60);
	return $lang;//return array_merge($lang,unserialize($lang['dictionary']));
}

//выбирает слово из словаря по ключу, оборачивает в блок для редактирования
function i18n ($str,$editable=false) {
	global $lang;
	$data = explode('|',$str);
	if (!isset($lang[$data[0]])) {
		if (file_exists(ROOT_DIR.'/files/languages/'.$lang['id'].'/dictionary/'.$data[0].'.php')) require_once(ROOT_DIR.'/files/languages/'.$lang['id'].'/dictionary/'.$data[0].'.php');
		else trigger_error('dictionary '.$str, E_USER_DEPRECATED);
	}
	if ($editable!=false && !isset($_GET['i18n']) && access('editable dictionary')) {
		$string = isset($lang[$data[0]][$data[1]]) ? $lang[$data[0]][$data[1]] : '';
		return '<span class="editable_text" data-edit="'.$lang['id'].'|dictionary|'.$str.'">'.$string.'</span>';
	}
	else return (isset($lang[$data[0]][$data[1]]) && !isset($_GET['i18n'])) ? $lang[$data[0]][$data[1]] : str_replace('%s',$str, '{%s}');
}

//выбирает слово из словаря по ключу, оборачивает в блок для редактирования
function a18n ($str) {
	global $a18n;
	return (isset($a18n[$str])) ? $a18n[$str] : $str;
}
?>