<?php

define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

$file = isset($_GET['file']) ? $_GET['file']:''; //echo $file.'<br />';

if (!preg_match("/^[a-z_]+$/", $file)) die('ошибка запроса');

if(!is_file(ROOT_DIR."cron/$file.php")) die('нет такого файла');

require_once(ROOT_DIR."cron/$file.php");

/**/
function cron_languages () {
	$query = "SELECT * FROM languages";
	$result = mysql_query($query); echo mysql_error();
	while ($q=mysql_fetch_assoc($result)) {
		$ilang[$q['id']]= array_merge($q,json_decode($q['dictionary'],true));
	}
	return $ilang;
}
function cron_mailers ($template) {
	global $ilang;
	$query = "SELECT name1,name2,email FROM mailer WHERE template='".mysql_real_escape_string($template)."' ORDER BY id LIMIT 1";
	$result = mysql_query($query); //echo $query
	while ($q = mysql_fetch_assoc($result)) {
		$imailer[1]['name'] = $q['name1'];
		$imailer[2]['name'] = $q['name2'];
		$imailer[1]['email'] = $imailer[2]['email'] = $q['email'];
	}
	return $imailer;
}
function cron_moduleses () {
	$query = "SELECT url,module,language FROM pages WHERE module!='pages'";
	$result = mysql_query($query); //echo $query
	while ($q = mysql_fetch_assoc($result)) {
		$moduleses[$q['language']][$q['module']] = $q['url'];
	}
	return $moduleses;
}
function cron_configs () {
	global $ilang;
	$result = mysql_query("SELECT * FROM config");
	$config = mysql_fetch_assoc($result);
	foreach ($ilang as $k=>$v) {
		$iconfig[$k] = cron_config($k);
		$iconfig[$k] = array_merge($iconfig[$k],$config);
	}
	return $iconfig;
}
function cron_config ($id) {
	global $ilang;
	$lang = $ilang[$id];
	include(ROOT_DIR."lang".$id.".php");
	include(ROOT_DIR."functions/common_conf.php");
	return $config;
}
function cron_mailer ($template,$q) {
	global $iconfig,$ilang,$imodules,$imailer;
	$lang		= $ilang[$q['language']];
	$modules	= $imodules[$q['language']];
	$config		= $iconfig[$q['language']];
	$mailer		= $imailer[$q['language']]; //print_R($mailer);
	//иключительные ситуации
	ob_start(); // echo to buffer, not screen
	include (ROOT_DIR.'mailer/'.$lang['url'].'/'.$template.'.php');
	$text = '';
	if (isset($q['first_name'])) {
		$text = $lang['mailer_head'];
		foreach ($q as $k=>$v) if (!is_array($v)) $text = str_replace ("{".$k."}", $v, $text);
	}
	$text.= ob_get_clean(); // get buffer contents
	$text.= $lang['mailer_text']; // get buffer contents
	//echo $text;
	return email($mailer['email'], $q['email'], $mailer['name'], $text);

}

?>
