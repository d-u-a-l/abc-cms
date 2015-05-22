<?php

if (isset($_GET['u']) && $_GET['u']=='clear') mysql_query("TRUNCATE `logs`");

$config['logs']['type'] = array(
	1	=>	'insert',
	2	=>	'update',
	3	=>	'delete',
);

$where = (isset($get['type']) && $get['type']>0) ? 'AND l.type = '.$get['type'].' ' : '';
$where.= (isset($get['user']) && $get['user']>0) ? 'AND l.user = '.$get['user'].' ' : '';

$query = "
	SELECT l.*,u.email
	FROM logs l
	LEFT JOIN users u ON u.id = l.user
	WHERE 1 $where
";

$filter[] = array('user',"SELECT u.id,u.email name FROM users u RIGHT JOIN logs l ON l.user = u.id GROUP BY u.id",'пользователь');
$filter[] = array('type',$config['logs']['type'],'действие');

$content = '<div style="float:right; padding:7px 0 0"><a href="?m=logs&u=clear" onclick="if(confirm(\'Подтвердите\')) {} else return false;">Очистить</a></div>';

$table = array(
	'_edit'=>false,
	'id'=>'id:desc',
	'email'=>'text',
	'type'=>$config['logs']['type'],
	'module'=>'<a href="/admin.php?m={module}&id={parent}">{module}->{parent}</a>',
	'date'=>'text',
	'_delete'=>false
);