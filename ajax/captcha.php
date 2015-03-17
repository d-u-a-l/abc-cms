<?php

//session_start();
if (isset($_SESSION['captcha'])) $value = $_SESSION['captcha'];
else {
	$value = mt_rand(1000000,9999999);
	$_SESSION['captcha'] = $value;
}
echo $value;

?>