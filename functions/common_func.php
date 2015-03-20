<?php

//обрезание обратных слешев в $_REQUEST данных
function stripslashes_smart($post) {
	if (get_magic_quotes_gpc()) {
		if (is_array($post)) {
			foreach ($post as $k=>$v) {
				$q[$k] = stripslashes_smart($v);
			}
		}
		else $q = stripslashes($post);
	}
	else $q = $post;
	return $q;
}

//создание урл из $_GET
function build_query($key = '') {
	$get = $_GET;
	if ($key) {
		$array = explode(',',$key);
		foreach ($array as $k=>$v) unset($get[$v]);
	}
	return http_build_query($get);
}

?>