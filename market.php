<?php

define('ROOT_DIR', dirname(__FILE__).'/');

/*$cache = 60*60*1; //1 час
$file = ROOT_DIR.'market.xml';
if (file_exists($file) && (time()-$cache)<filemtime($file)) {
	echo file_get_contents($file);
	die();
}/**/

// загрузка функций **********************************************************
header('Content-type: text/xml; charset=UTF-8');
require_once(ROOT_DIR.'config_db.php');
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
require_once(ROOT_DIR.'functions/index_func.php');	//общие функции
require_once(ROOT_DIR.'functions/index_conf.php');	//настройки

$server_name = $_SERVER['SERVER_NAME'];
$categories = mysql_select("SELECT id,url,name,parameters,parent FROM shop_categories WHERE display=1",'rows_id');
$shop_parameters = mysql_select("SELECT * FROM shop_parameters WHERE display=1 ORDER BY rank DESC ",'rows_id');

$xml = '<?xml version="1.0" encoding="UTF8"?>';
$xml.= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
$xml.= '<yml_catalog date="'.date('Y-m-d h:m').'">';
$xml.= '<shop>';
$xml.= '<name>'.$lang['market_name'].'</name>';
$xml.= '<company>'.$lang['market_company'].'</company>';
$xml.= '<url>http://'.$server_name.'/</url>';
$xml.= '<currencies>';
$xml.= '<currency id="'.$lang['market_currency'].'" rate="1"/>';
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
	$xml.= '<currencyId>'.$lang['market_currency'].'</currencyId>';
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
		$xml.= $q['parameters'];
	}
	$xml.= '</description>';
	//$xml.= '<sales_notes>Минимальная сумма заказа 10 000 рублей.</sales_notes>';
	$xml.= '</offer>';
}
$xml.= '</offers>';
$xml.= '</shop>';
$xml.= '</yml_catalog>';

$fp = fopen(ROOT_DIR.'market.xml','w');
fwrite($fp, $xml);
fclose($fp);

echo $xml;

?>