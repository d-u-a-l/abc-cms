<?php
error_reporting(E_ALL);
//error_reporting(0);
require_once('functions/global_conf.php');
require_once('functions/config.php');

$config['cache'] = false;
$config['domain'] =  $_SERVER['HTTP_HOST'];

$cache = 60*60*24;
$file = ROOT_DIR.'sitemap.xml';
//если не указана генерация файла или кеш еще актуальный
if (file_exists($file) AND (@$config['sitemap_generation']==0 OR (time()-$cache)<filemtime($file))) {
	echo file_get_contents($file);
	die();
}

require_once(ROOT_DIR.'config_db.php');
require_once(ROOT_DIR.'functions/common_func.php');
require_once(ROOT_DIR.'functions/index_conf.php');	//функции для сайта

header('Content-type: text/xml; charset=UTF-8');
$content = '<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url><loc>http://'.$config['domain'].'/</loc></url>';

//генерация всех ссылок
if (@$config['sitemap_generation']==1) {
	$urls['pages'] = sitemap("SELECT url FROM pages WHERE display=1 ORDER BY left_key",'/{url}/');
	if (isset($modules['news']))
		$urls['news'] = sitemap("SELECT id,url FROM news WHERE display=1 ORDER BY date DESC",'/'.$modules['news'].'/{id}-{url}/');
	if (isset($modules['gallery']))
		$urls['gallery'] = sitemap("SELECT id,url FROM gallery WHERE display=1 ORDER BY rank DESC",'/'.$modules['gallery'].'/{id}-{url}/');
	if (isset($modules['shop'])) {
		$urls['shop_products'] = sitemap("
			SELECT sp.url,sp.id,sp.name, sc.url category_url,sc.id category_id
			FROM shop_products sp, shop_categories sc
			WHERE sp.display=1 AND sc.display=1
			ORDER BY sc.left_key,sp.id
		",'/'.$modules['shop'].'/{category_id}-{category_url}/{id}-{url}/');
		$urls['shop_categories'] = sitemap("SELECT id,url FROM shop_categories WHERE display=1 ORDER BY left_key",'/'.$modules['shop'].'/{id}-{url}/');
	}
	foreach ($urls as $key=>$val) if (is_array($val)) foreach ($val as $k=>$v)$content.= '
	<url><loc>http://'.$config['domain'].$v.'</loc></url>';
}
//генерация только не в индексе
elseif (@$config['sitemap_generation']==2) {
	$urls[] = sitemap("SELECT url FROM seo_pages WHERE exist=1 AND yandex_index=0 ORDER BY yandex_check",'{url}');
	foreach ($urls as $key=>$val) if (is_array($val)) foreach ($val as $k=>$v)$content.= '
	<url><loc>http://'.$config['domain'].$v.'</loc></url>';
}
$content.= '
</urlset>';

//запись в файл
$fp = fopen(ROOT_DIR.'sitemap.xml', 'w');
fwrite($fp, $content);

echo $content;

function sitemap ($query,$url) {	preg_match_all('/{(.*?)}/',$url,$matches,PREG_PATTERN_ORDER);	$result = mysql_query($query); echo mysql_error();
	$data = array();
	while ($q = mysql_fetch_assoc($result)) {		foreach($matches[1] as $k=>$v) {			$matches2[1][$k] = isset($q[$v]) ? $q[$v] : '';
		}		$data[] = str_replace($matches[0],$matches2[1],$url);
	}
	return $data;
}

?>
