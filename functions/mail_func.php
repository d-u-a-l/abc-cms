<?php

/**
 * функции для работы с почтой
 * todo
 * сделать отправку через smtp
 */

/**
 * отправка email
 * @param string $sender - отправитель
 * @param string $receiver - получатель
 * @param string $subject - тема письма
 * @param string $text - текст письма
 * @param string $reply - кому ответить
 * @param array $files - массив файлов array('название файла'=>'путь к файлу','название файла'=>'путь к файлу')
 * @return bool - отправлено или нет
 */
function email($sender,$receiver,$subject,$text,$reply=false,$files = array()) {
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
	foreach ($receivers as $k=>$v) {
		if ($k>0) sleep(1); //делаем паузу перед отправлением второго письма
		$return = mail(trim($v),$subject,$multipart,$headers) ? $return : false;
	}
	//возвращаем false если хотя бы одно письмо не отправлено
	return $return;
}

/**
 * отправляет письма по шаблону в таблице letter_templates
 * @param string $template - letter_templates.name
 * @param int $language - ID языка
 * @param array $q - массив данных
 * @param string $receiver - получатель
 * @param string $sender - отправитель
 * @param string $reply - ответить
 * @param array $files - массив файлов
 * @return boolean - отправлено или нет
 * @see email
 */
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
		if ($letter['template']) {
			if (!function_exists('html_array')) require_once(ROOT_DIR.'functions/html_func.php');
			$text = html_array('letter_templates/'.$letter['template'],$text);
		}
		//echo ($text).'<br />';
		return email ($sender,$receiver,$subject,$text,$reply,$files);
	}
}


/**
 * функция для отправки письма через smtp - не готово
 * @param $smtp_server
 * @param $smtp_port
 * @param $smtp_login
 * @param $smtp_password
 * @param $from
 * @param $from_name
 * @param $to
 * @param $subject
 * @param $message
 * @return bool
 */
function smtp_mail ($smtp_server,$smtp_port,$smtp_login,$smtp_password,$from,$from_name,$to,$subject,$message) {
	$result = false;
	$from_name = base64_encode($from_name);
	$subject = base64_encode($subject);
	$message = base64_encode($message);
	$message = "Content-Type: text/plain; charset=\"utf-8\"\r\nContent-Transfer-Encoding: base64\r\nUser-Agent: The Bat!\r\nMIME-Version: 1.0\r\n\r\n".$message;
	$subject="=?utf-8?B?{$subject}?=";
	$from_name="=?utf-8?B?{$from_name}?=";
	try {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket < 0) {
			throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
		}
		if (socket_connect($socket, $smtp_server, $smtp_port) === false) {
			throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
		}
		smtp_read($socket);
		smtp_write($socket, 'EHLO '.$login);
		smtp_read($socket);
		smtp_write($socket, 'AUTH LOGIN');
		smtp_read($socket);
		smtp_write($socket, base64_encode($login));
		smtp_read($socket);
		smtp_write($socket, base64_encode($password));
		smtp_read($socket);
		smtp_write($socket, 'MAIL FROM:<'.$from.'>');
		smtp_read($socket);
		smtp_write($socket, 'RCPT TO:<'.$to.'>');
		smtp_read($socket);
		smtp_write($socket, 'DATA');
		smtp_read($socket);
		$message = "FROM:".$from_name."<".$from.">\r\n".$message;
		$message = "To: $to\r\n".$message;
		$message = "Subject: $subject\r\n".$message;
		//date_default_timezone_set('UTC');
		$utc = date('r');

		$message = "Date: $utc\r\n".$message;
		smtp_write($socket, $message."\r\n.");
		smtp_read($socket);
		smtp_write($socket, 'QUIT');
		smtp_read($socket);
		$result = true;

	}
	catch (Exception $e) {
		echo "\nError: ".$e->getMessage();
	}
	if (isset($socket)) {
		socket_close($socket);
	}

	return $result;
}

function smtp_read($socket) {
	$read = socket_read($socket, 1024);
	if ($read{0} != '2' && $read{0} != '3') {
		if (!empty($read)) {
			throw new Exception('SMTP failed: '.$read."\n");
		}
		else {
			throw new Exception('Unknown error'."\n");
		}
	}
}

function smtp_write($socket, $msg) {
	$msg = $msg."\r\n";
	socket_write($socket, $msg, strlen($msg));
}

?>