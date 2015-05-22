<?php

$table = array(
	'id'		=>	'date:desc name url user title id',
	'name'		=>	'',
	'title'		=>	'',
	'url'		=>	'',
	'date'		=>	'date',
	'display'	=>	'boolean'
);

$query = "
	SELECT news.*,
		u.email login
	FROM news
	LEFT JOIN users u ON u.id = news.user
	WHERE 1
";

$form[] = array('input td7','name',true);
$form[] = array('input td3','date',true);
$form[] = array('checkbox','display',true);
$form[] = array('tinymce td12','text',true);
$form[] = array('seo','seo url title keywords description',true);