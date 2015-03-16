<?php

//header('Content-type: text/html; charset=UTF-8');

//общие функции
require_once('functions/global_conf.php');
require_once(ROOT_DIR.'functions/config.php');	//общие функции

//if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die();

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (!preg_match('/^[a-zA-Z_]+$/', $file) || !is_file(ROOT_DIR.'ajax/'.$file.'.php')) die();

require_once(ROOT_DIR."ajax/$file.php");

?>