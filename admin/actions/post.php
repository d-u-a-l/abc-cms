<?php

// POST - ������� ��������������
require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
//������ ����� ������ ���� ���� ��������� � ������� $fieldset
//if (array_key_exists($get['name'],$fieldset)) {
	if (mysql_fn('update',$get['m'],array($get['name']=>$get['value'],'id'=>$get['id']))) {
	//����������� ��������
		mysql_fn('insert','logs',array(
			'user'		=>	$user['id'],
			'date'		=>	date('Y-m-d H:i:s'),
			'parent'	=>	$get['id'],
			'module'	=>	$get['m'],
			'type'		=>	2
		));
	}
//}

?>