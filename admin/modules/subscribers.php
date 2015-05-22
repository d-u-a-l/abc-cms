<?php

$filter[] = array('search');

if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(subscribers.email) like  '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT subscribers.*
	FROM subscribers
	WHERE 1 $where
";

$table = array(
	'id'		=>	'id:desc date email name',
	'email'		=>	'',
	'date'		=>	'date',
	'display'	=>	'display'
);

$form[] = array('input td7','email',true);
$form[] = array('input td3','date',true);
$form[] = array('checkbox','display',true);