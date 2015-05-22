<?php

// POST - БЫСТРОЕ РЕДАКТИРОВАНИЕ
require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
//запрос будет только если ключ находится в массиве $fieldset
//if (array_key_exists($get['name'],$fieldset)) {
	if (mysql_fn('update',$get['m'],array($get['name']=>$get['value'],'id'=>$get['id']))) {
	//логирование действия
		mysql_fn('insert','logs',array(
			'user'		=>	$user['id'],
			'date'		=>	date('Y-m-d H:i:s'),
			'parent'	=>	$get['id'],
			'module'	=>	$get['m'],
			'type'		=>	2
		));
	}
//}
