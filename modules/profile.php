<?php

//востановление пароля
if (isset($_GET['email'])) $user = user('remind');

if (access('user auth')==false) {
	die(header('location:/'.$modules['login'].'/'));
}
$config['profile'] = array(
	'user_edit'	=>	i18n('profile|user_edit'),
);
if (isset($modules['basket']))
	$config['profile']['orders'] = i18n('basket|orders');

$html['module2'] = '';
if (array_key_exists($u[2],$config['profile']) && file_exists('modules/profile/'.$u[2].'.php')) {	$html['module2'] = $u[2];
	$page['name'] = $config['profile'][$u[2]];
	require_once('modules/profile/'.$u[2].'.php');
	$breadcrumb['module'][] = array($config['profile'][$u[2]],'/'.$modules['profile'].'/'.$u[2].'/');
}
else {
	$html['profile_menu'] = html_array('profile/menu',$config['profile']);
}
?>
