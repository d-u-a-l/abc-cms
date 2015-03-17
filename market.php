<?php

/**
 * файл генерирует xml документ для yandex market
 * доступен по адресу /market.xml
 * в .htaccess есть настройка RewriteRule ^market.xml$ market.php [L]
 */

// загрузка настроек *********************************************************
define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
//require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
//require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
//require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
//require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

/*$cache = 60*60*1; //1 час
$file = ROOT_DIR.'market.xml';
if (file_exists($file) && (time()-$cache)<filemtime($file)) {
	echo file_get_contents($file);
	die();
}/**/

header('Content-type: text/xml; charset=UTF-8');

//основной язык
$lang = lang(1); //print_r($lang);

//список модулей на сайте
$modules = mysql_select("SELECT url name,module id FROM pages WHERE module!='pages' AND language=".$lang['id']." AND display=1",'array',60*60);

$server_name = $_SERVER['SERVER_NAME'];
$categories = mysql_select("SELECT id,url,name,parameters,parent FROM shop_categories WHERE display=1",'rows_id');
$shop_parameters = mysql_select("SELECT * FROM shop_parameters WHERE display=1 ORDER BY rank DESC ",'rows_id');

$xml = '<?xml version="1.0" encoding="UTF8"?>';
$xml.= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
$xml.= '<yml_catalog date="'.date('Y-m-d h:m').'">';
$xml.= '<shop>';
$xml.= '<name>'.i18n('market|name').'</name>';
$xml.= '<company>'.i18n('market|company').'</company>';
$xml.= '<url>http://'.$server_name.'/</url>';
$xml.= '<currencies>';
$xml.= '<currency id="'.i18n('market|currency').'" rate="1"/>';
$xml.= '</currencies>';
$xml.= '<categories>';
//============
foreach ($categories as $k=>$v) {
	$xml.= '
<category id="'.$v['id'].'" parentId="'.$v['parent'].'">'.$v['name'].'</category>';
}
$xml.= '</categories>';
$xml.= '<offers>';
//==========
$result = mysql_query("SELECT * FROM shop_products WHERE market=1 AND price>0");
while ($q=mysql_fetch_assoc($result)) {
	$xml.= '
<offer id="'.$q['id'].'" available="true">';
	$xml.= '<url>http://'.$server_name.'/'.$modules['shop'].'/'.$q['category'].'-'.$categories[$q['category']]['url'].'/'.$q['id'].'-'.$q['url'].'/</url>';
	$xml.= '<price>'.$q['price'].'</price>';
	$xml.= '<currencyId>'.i18n('market|currency').'</currencyId>';
	$xml.= '<categoryId>'.$q['category'].'</categoryId>';
	if (file_exists(ROOT_DIR.'files/shop_products/'.$q['id'].'/img/'.$q['img']))
		$xml.= '<picture>http://'.$server_name.'/files/shop_products/'.$q['id'].'/img/'.$q['img'].'</picture>';
	$xml.= '<name>'.htmlspecialchars($q['name']).'</name>';
	$xml.= '<description>';
	$parameters = $categories[$q['category']]['parameters'] ? unserialize($categories[$q['category']]['parameters']) : false;
	if ($parameters) foreach($parameters as $k=>$v) if ($q['p'.$k]!=0 AND isset($shop_parameters[$k])) {
		$name =  $shop_parameters[$k]['name'];
		$values = $shop_parameters[$k]['values'] ? unserialize($shop_parameters[$k]['values']) : array();
		if (in_array($shop_parameters[$k]['type'],array(1,3))) $name.=': '.@$values[$q['p'.$k]];
		elseif ($shop_parameters[$k]['type']==2) $name.= ': '.$q['p'.$k];
		if ($shop_parameters[$k]['units']) $name.= ' '.$shop_parameters[$k]['units'];
		$name.= '; ';
		$xml.= htmlspecialchars($name);
	}
	$xml.= '</description>';
	//$xml.= '<sales_notes>Минимальная сумма заказа 10 000 рублей.</sales_notes>';
	$xml.= '</offer>';
}
$xml.= '</offers>';
$xml.= '</shop>';
$xml.= '</yml_catalog>';


//запись в файл
$fp = fopen(ROOT_DIR.'market.xml','w');
fwrite($fp, $xml);
fclose($fp);
/* */

echo $xml;

?>