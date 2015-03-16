<?php

if ($u[2]) $error++;

//обрабока формы
if (count($_POST)>0) {
	//загрузка функций для формы
	require_once(ROOT_DIR.'functions/index_form.php');

	//определение значений формы
	$fields = array(
		'email'			=>	'required email',
		'name'			=>	'required text',
		'text'			=>	'required text',
		'captcha'		=>	'required captcha2'
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);

	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	//если нет ошибок то отправляем сообщение
	if (count($message)==0) {		unset($_SESSION['captcha'],$post['captcha']); //убиваем капчу чтобы второй раз не отправлялось		//прикрепленные файлы
		$files = array();
		$post['files'] = array();
		if (isset($_FILES['attaches']['name']) AND is_array($_FILES['attaches']['name'])) {
			foreach ($_FILES['attaches']['name'] as $k=>$v) if ($v) {				$name = trunslit($v);
				$files[$name] = $_FILES['attaches']['tmp_name'][$k];
				$post['files'][] = array(
					'name'=>$v,
					'file'=>$name
				);
			}
		}
		//запись сообщения в базу вместе с файлами
		$post['files'] = count($post['files']) ? serialize($post['files']) : '';
		$post['date'] = date('Y-m-d H:i:s');
		$post['id'] = mysql_fn('insert','feedback',$post);
		if ($post['files']) {			$i = 0;
			foreach ($files as $k=>$v) {				$path = ROOT_DIR.'files/feedback/'.$post['id'].'/files/'.$i.'/';				mkdir($path,0755,true);				copy($v,$path.$k);
				$i++;
			}
		}

		//5-й параметр кому ответить
		mailer('feedback',$lang['id'],$post,false,$post['email'],false,$files);
		$post['success'] = 1;
		/*if (email(
			$config['email'],								//отправитель
			$config['email'],								//получатель
			$_SERVER['SERVER_NAME'].' - новое сообщение',	//тема сообщения
			html_array('mailer/feedback',$post),			//текст сообщения
			$post['email'],									//ответить
			$files											//файлы
		)) $post['success'] = 1;
		else $message[] = $lang['msg_error_email'];*/
	}
	if (count($message)>0) $post['message'] = $message;
}
else $post = array();

//вывод шаблона
$html['content'] = html_array('form/feedback',@$post);

?>
