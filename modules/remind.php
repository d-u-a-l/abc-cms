<?php

//обрабока формы
if (count($_POST)>0) {
	//загрузка функций для формы
	require_once(ROOT_DIR.'functions/form_func.php');
	//определение значений формы
	$fields = array(
		'email'		=>	'required email',
		'captcha'	=>	'required captcha2'
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);

	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	if (count($message)==0) {
		$result = mysql_query("
			SELECT *
			FROM users
			WHERE email = '".mysql_real_escape_string(strtolower($post['email']))."'
			LIMIT 1
		");
		if (mysql_num_rows($result)==1) {
			$q = mysql_fetch_array($result);
			mailer('remind',$lang['id'],$q,$post['email']);
			$post['success'] = 1;
			/*if (email(
				$config['email'],
				$post['email'],
				'Востановление пароля на сайте '.$_SERVER['SERVER_NAME'],
				html_array('mailer/remind',$q)
			)) $post['success'] = 1;
			else $message[] = $lang['msg_error_email'];//'Произошла ошибка с отправлением письма, если это повторится, сообщите администартору '.$config['email'].'!';
			*/
		}
		else $message[] =i18n('validate|no_email',true);//'Данный E-mail не закреплён ни за одним пользователем!';
	}
	if (count($message)>0) $post['message'] = $message;
}

//вывод шаблона
$post['text'] = $page['text'];
$html['content'] = html_array('profile/remind',@$post);

?>

