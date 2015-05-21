<?php

$a18n['display'] = 'просмотрено';
$a18n['name'] = 'имя';

$table = array(
	'id'		=>	'date:desc name email',
	'name'		=>	'',
	'email'		=>	'',
	'date'		=>	'date',
	'display'	=>	'display'
);

$filter[] = array('search');

$where = '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(feedback.name) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(feedback.email) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(feedback.text) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "SELECT * FROM feedback WHERE 1 $where";

$form[] = array('input td4','name',true);
$form[] = array('input td4','email',true);
$form[] = array('input td2','date',true);
$form[] = array('checkbox','display',true);
$form[] = array('textarea td12','text',true);
$form[] = array('textarea td12','comment',true);

$form[] = array('file_multi','files','файлы','',array('name'=>'input'));