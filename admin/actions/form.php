<?php

// FORM - ЗАГРУЗКА ФОРМЫ РЕДАКТИРОВАНИЯ
if ($post = mysql_select("SELECT * FROM ".$get['m']." WHERE id = '".intval($get['id'])."'",'row')) {
	foreach ($filter as $f) {
		if (isset($post[$f[0]])) $get[$f[0]] = $post[$f[0]];
	}
	//создание масива $post[depend]
	if (isset($config['depend'][$get['m']])) {
		foreach ($config['depend'][$get['m']] as $k=>$v) {
			$result = mysql_query("SELECT parent FROM `$v` WHERE child = '".intval($get['id'])."'"); echo mysql_error();
			while ($q = mysql_fetch_assoc($result)) {
				$post['depend'][$v][] = $q['parent'];
			}
		}
	}
//значения по умолчанию для новой записи
} else {
	$post = $get;
	$post['date'] = date('Y-m-d H:i:s');
	$post['rank'] = $post['seo'] = $post['change'] = $post['display'] = 1;
	$post['user'] = $user['id'];
}
require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
require_once(ROOT_DIR.'admin/templates/admin_form.php');
