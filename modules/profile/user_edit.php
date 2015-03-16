<?php
require_once(ROOT_DIR.'functions/index_form.php');

//обрабока формы
if (count($_POST)>0) {
	//создание массива $post
	$fields = array(
		'password'		=> 'required password',
	);	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);
	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	$post['id']		= $user['id'];
	$post['hash']	= $post['password']!='0000000000' ? md5($user['email'].md5($post['password'])) : $user['hash'];
	$post['fields'] = serialize(stripslashes_smart(@$_POST['fields']));

	unset($post['password']);

	if (count($message)==0) {
		if (mysql_fn('update','users',$post)) {			$_SESSION['user'] = $user = array_merge($user,$post);
			$message[]= 'Изменения внесены успешно!';
		}
		else $message[]= 'Никаких изменений небыло внесено!';
	}
	$post['message'] = $message;
}
else $post = $user;

//вывод нтмл
$html['content'] = html_array('profile/edit',$post);

?>
