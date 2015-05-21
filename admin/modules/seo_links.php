<?php

$a18n['limit']		= 'лимит';
$a18n['img']		= 'картинка';
$a18n['keyword']	= 'ключевой запрос';

//удаление всех страниц
if ($get['u']=='delete_links') {
	//удаляем связи
	mysql_query("TRUNCATE `seo_links-pages`");
	mysql_query("TRUNCATE seo_links");
}

$delete['delete'] = "DELETE FROM `seo_links-pages` WHERE parent = '".$get['id']."'";

$filter[] = array('search');

$table = array(
	'id'		=> 'id:desc name url limit',
	'name'		=> '<a href="{url}" target="_blank">{name}</a>',
	'keyword'	=> '',
	'url'		=> '',
	'img'		=> '',
	'limit'		=> '',
	'count'		=> 'text'
);

$where = '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(seo_links.name) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(seo_links.keyword) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(seo_links.url) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT seo_links.*,COUNT(d.id) count
	FROM seo_links
	LEFT JOIN `seo_links-pages` d ON d.parent=seo_links.id
	WHERE 1 $where
	GROUP BY seo_links.id
";

$filter[] = '<a href="/admin.php?m=seo_links&u=delete_links" onclick="if(confirm(\'подтвердите удаление!\')) {} else return">удалить все ссылки</a>';

$form[] = array('input td4','name',true);
$form[] = array('input td4','keyword',true);
$form[] = array('input td4','url',true);
$form[] = array('input td4','img',true);
$form[] = array('input td1','limit',true);