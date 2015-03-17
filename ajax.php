<?php

/**
 * Основной уро для аджакс запросов типа /ajax.php?file={file}
 * {file} - имя файла в папке /ajax/{file}.php
 */

session_start();

define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

//если запрос не из сайта то умирать
//if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die();

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (!preg_match('/^[a-zA-Z_]+$/', $file) || !is_file(ROOT_DIR.'ajax/'.$file.'.php')) die();

require_once(ROOT_DIR."ajax/$file.php");

?>