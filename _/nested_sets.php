<?php

/*
восстановление дерева по полю parent - 4 уровня вложенности
*/

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'config_db.php');//доступ к ДБ
include_once (ROOT_DIR.'functions/common_func.php');

$pages = mysql_select("SELECT id,parent FROM pages ORDER BY left_key",'rows_id');
foreach ($pages as $k=>$v) {
	$pages2[$v['parent']][] = $v['id'];
}
$i=1;
foreach ($pages2[0] as $k1=>$v1) {
	$pages[$v1]['left_key']	= $i++;
	if (isset($pages2[$v1])) foreach ($pages2[$v1] as $k2=>$v2) {
		$pages[$v2]['left_key']	= $i++;
		if (isset($pages2[$v2])) foreach ($pages2[$v2] as $k3=>$v3) {
			$pages[$v3]['left_key']	= $i++;
			if (isset($pages2[$v3])) foreach ($pages2[$v4] as $k4=>$v4) {
				$pages[$v4]['left_key']	= $i++;
				$pages[$v4]['right_key']= $i++;
				$pages[$v4]['level']= 4;
				mysql_fn('update','pages',$pages[$v4]);
			}
			$pages[$v3]['right_key']= $i++;
			$pages[$v3]['level']= 3;
			mysql_fn('update','pages',$pages[$v3]);
		}
		$pages[$v2]['right_key']= $i++;
		$pages[$v2]['level']= 2;
		mysql_fn('update','pages',$pages[$v2]);
	}
	$pages[$v1]['right_key']= $i++;
	$pages[$v1]['level']= 1;
	mysql_fn('update','pages',$pages[$v1]);
}


?>