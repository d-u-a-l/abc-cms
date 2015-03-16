<?php

//отправка email
function email($sender,$receiver,$subject,$text,$reply=false,$files = array()) {
	//$sender - отправитель
	//$receiver - получатель
	//$subject - тема псиьма
	//$text - текст письма
	//$files - прикрепленные файлы
	//global $config;
	$subject = '=?UTF-8?B?'.base64_encode(filter_var($subject)).'?=';
	$sitename = $_SERVER['SERVER_NAME'];
	$sitename = '=?UTF-8?B?'.base64_encode(filter_var($sitename, FILTER_SANITIZE_STRING )).'?=';
	//без файлов
	$headers = "MIME-Version: 1.0".PHP_EOL;
	//если письма не доходят то отправителем надо ставить емейл который добавлен на сервере
	$headers.= "From: ".$sitename." <".$sender.">".PHP_EOL;
	$headers.= "Return-path: ".$sender.PHP_EOL;
	if ($reply) $headers.= "Reply-To: ".$reply.PHP_EOL;
	$headers.= "X-Mailer: PHP/".phpversion().PHP_EOL;
	if (!is_array($files) OR count($files)==0) {
		$headers .= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
		$multipart = $text;
	}
	else {
		$boundary = "--".md5(uniqid(time()));
		$headers.="Content-Type: multipart/mixed; boundary=\"".$boundary."\"".PHP_EOL;
		$multipart = "--".$boundary.PHP_EOL;
		$multipart.= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
		$multipart.= "Content-Transfer-Encoding: base64".PHP_EOL.PHP_EOL;
		$text = chunk_split(base64_encode($text)).PHP_EOL.PHP_EOL;
		$multipart.= stripslashes($text);
		//$count = count($files);
		foreach($files as $k=>$v) if (is_file($v)){
			$fp = fopen($v, "r");
			if ($fp) {
				$content = fread($fp, filesize($v));
				$multipart.= "--".$boundary.PHP_EOL;
				$multipart.= 'Content-Type: application/octet-stream'.PHP_EOL;
				$multipart.= 'Content-Transfer-Encoding: base64'.PHP_EOL;
				$multipart.= 'Content-Disposition: attachment; filename="=?UTF-8?B?'.base64_encode(filter_var($k,FILTER_SANITIZE_STRING )).'?="'.PHP_EOL.PHP_EOL;
				$multipart.= chunk_split(base64_encode($content)).PHP_EOL;
			}
			fclose($fp);
		}
		$multipart.= "--".$boundary."--".PHP_EOL;
	}
	$receivers = explode(',',$receiver);
	$return = true;
	foreach ($receivers as $k=>$v) {		if ($k>0) sleep(1); //делаем паузу перед отправлением второго письма		$return = mail(trim($v),$subject,$multipart,$headers) ? $return : false;
	}
	//возвращаем false если хотя бы одно письмо не отправлено
	return $return;
}

function mailer($template,$language,$q,$receiver=false,$sender=false,$reply=false,$files=false) {
	//echo "SELECT * FROM letter_templates WHERE name='".$template."'";
	if ($letter = mysql_select("SELECT * FROM letter_templates WHERE name='".$template."'",'row')) {
		global $lang,$config,$modules;
		if ($receiver==false) $receiver = $letter['receiver'] ? $letter['receiver'] : $config['receiver'];
		if ($sender==false) $sender = $letter['sender'] ? $letter['sender'] : $config['sender'];
		//print_r($letter);
		ob_start();
		include (ROOT_DIR.'files/letter_templates/'.$letter['id'].'/'.$language.'/subject.php');
		$subject = ob_get_clean();
		ob_start(); // echo to buffer, not screen
		include (ROOT_DIR.'files/letter_templates/'.$letter['id'].'/'.$language.'/text.php');
		$text = ob_get_clean(); // get buffer contentshtml_array('letter_templates/'.$q);
		//echo '<b>'.$subject.'</b><br />'.$text.'<br /><br />';
		if ($letter['template']) {			if (!function_exists('html_array')) require_once(ROOT_DIR.'functions/html_func.php');
			$text = html_array('letter_templates/'.$letter['template'],$text);
		}
		//echo ($text).'<br />';
		email ($sender,$receiver,$subject,$text,$reply,$files);
	}
}

?>