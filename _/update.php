<?php

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'config_db.php');//доступ к ДБ
include_once (ROOT_DIR.'functions/common_imgs.php');
include_once (ROOT_DIR.'functions/common_func.php');

echo ROOT_DIR;


//список скл запросов
$queries = array(
"ALTER TABLE  `seo_pages` ADD  `articles` VARCHAR( 255 ) NOT NULL COMMENT  'ИД статтей' AFTER  `links`"
);
foreach ($queries as $query) {
	if ($query) {
		if (mysql_query($query)) echo '<div style="color:#00f">'.$query.'</div>';
		else echo '<div style="color:#f00">'.$query.' - '.mysql_error().'</div>';
	}
}/**/

/*
//добавление слов
$langs = array(
	'shop' =>	array(
		'qwert' =>	'йцуке'

	),
	'common' =>	array(
		'qwert' =>	'йцуке',
	)
);

$id = 1;
foreach ($langs as $key=>$val) {	echo '<b>'.$key.'</b><br />';	print_r($val);
	echo '<br /><br />';
	$lang = array();
	include(ROOT_DIR.'files/languages/'.$id.'/dictionary/'.$key.'.php');
	$lang[$key] = array_merge($lang[$key],$val);
	//print_r($lang[$key]);
	$str = '<?php'.PHP_EOL;
	$str.= '$lang[\''.$key.'\'] = array('.PHP_EOL;
	foreach ($lang[$key] as $k=>$v) {
		$str.= "	'".$k."'=>'".str_replace("'","\'",$v)."',".PHP_EOL;
	}
	$str.= ');';
	$str.= '?>';
	$fp = fopen(ROOT_DIR.'files/languages/'.$id.'/dictionary/'.$key.'.php', 'w');
	fwrite($fp,$str);
	fclose($fp);
}
/**/

//ресайз картинок
/*
$query = "SELECT * FROM shop_products ORDER BY id";
$result = mysql_query($query);
while ($q=mysql_fetch_assoc($result)) {
	echo $q['id'].'<br />';
	if ($q['img']) {
		$path = 'shop_products/'.$q['id'].'/img';
		$root = ROOT_DIR.'files/'.$path.'/';
		if (is_file($root.$q['img'])) {
			$param = array('m-'=>'resize 400x400');
			foreach ($param as $k=>$v) {
				$prm = explode(' ',$v);
				img_process($prm[0],$root.$q['img'],$prm[1],$root.$k.$q['img']);
				//если есть водяной знак
				//if (isset($prm[2])) img_watermark($root.$k.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$k.$file,@$prm[3]);
			}
		}
	}
	/*if ($q['imgs']) {
		$imgs = unserialize($q['imgs']);
		$path = 'shop_products/'.$q['id'].'/imgs';
		echo '<br />'.$path;
		$root = ROOT_DIR.'files/'.$path.'/';
		$param = array('p-'=>'cut 360x270');
		if (is_dir($root) AND $handle = opendir($root)) {
			while (false !== ($file = readdir($handle))) {
				if (isset($imgs[$file])) {
					$v1 = $root.$file.'/'.$imgs[$file]['file'];
					echo '<br />'.$v1;
					foreach ($param as $k=>$v) {
						$prm = explode(' ',$v);
						echo '<br />'.$v1;
						img_process($prm[0],$root.$file.'/'.$imgs[$file]['file'],$prm[1],$root.$file.'/'.$k.$imgs[$file]['file']);
						//если есть водяной знак
						//if (isset($prm[2])) img_watermark($root.$k.'/'.$file,ROOT_DIR.'templates/images/'.$prm[2],$root.$k.'/'.$file,@$prm[3]);
					}
				}
			}
			closedir($handle);
		}
	}*/
//}

/**/


/*
//из jsona c serilize
$data = mysql_select("SELECT id,dictionary FROM languages",'rows');
foreach ($data as $k=>$v) {
	$dictionary = json_decode($v['dictionary'],true);
	$v['dictionary'] = serialize($dictionary);
	mysql_fn('update','languages',$v);
}
$data = mysql_select("SELECT id,images FROM shop_products",'rows');
$data = mysql_select("SELECT id,`values` FROM shop_parameters",'rows');
$data = mysql_select("SELECT id,basket FROM orders",'rows');
$data = mysql_select("SELECT id,parameters FROM shop_categories",'rows');
$data = mysql_select("SELECT id,fields FROM users",'rows');
$data = mysql_select("SELECT id,access_admin FROM user_types",'rows');
*/


//обновление слов
/*
$data = mysql_select("SELECT id,dictionary FROM languages WHERE id=1",'row');
$dictionary = unserialize($data['dictionary'],true);
$dictionary['wrd_tutor_found']='Ученик нашел репетитора';
$data['dictionary'] = serialize($dictionary);
mysql_fn('update','languages',$data);
/**/

/*
//пересохранение словаря с массива в файлы
$data = mysql_select("SELECT id,dictionary FROM languages WHERE id=1",'row');
$dictionary = unserialize($data['dictionary']);
//print_r($dictionary);
$lang = array();
foreach ($dictionary as $k=>$v) {	//echo '-'.$k.'-'.substr($k,5).'<br />';
	$str = substr($k,0,4);	if ($str=='shop') {		$lang['shop'][substr($k,5)] = $v;
	}
	elseif ($str=='revi') {
		$lang['shop'][$k] = $v;
	}
	elseif ($str=='prof') {
		$lang['profile'][substr($k,8)] = $v;
	}
	elseif ($str=='bask') {
		$lang['basket'][substr($k,7)] = $v;
	}
	elseif ($str=='subs') {
		$lang['subscribe'][substr($k,10)] = $v;
	}
	elseif ($str=='mark') {
		$lang['market'][substr($k,7)] = $v;
	}
	elseif ($str=='feed') {
		$lang['feedback'][substr($k,9)] = $v;
	}
	elseif ($str=='msg_') {		$lang['validate'][substr($k,4)] = $v;
	}
	else $lang['common'][$k] = $v;

}
foreach ($lang as $key=>$val) {
	$str = '<?php'.PHP_EOL;
	$str.= '$lang[\''.$key.'\'] = array('.PHP_EOL;
	foreach ($val as $k=>$v) {		$str.= "	'".$k."'=>'".str_replace("'","\'",$v)."',".PHP_EOL;
	}
	$str.= ');';
	$str.= '?>';
	$fp = fopen(ROOT_DIR.'files/languages/1/dictionary/'.$key.'.php', 'w');
	fwrite($fp,$str);
	fclose($fp);
}
print_r($lang);
/**/


?>