<?php

$modules_site = array(
	'pages'			=> 'Текстовая страница',
	'index'			=> 'Главная',
	//'articles'		=> 'Статьи',
	'news'			=> 'Новости',
	'gallery'		=> 'Галерея',
	'shop'			=> 'Каталог',
	'basket'		=> 'Корзина',
	'feedback'		=> 'Обратная связь',
	'sitemap'		=> 'Карта сайта',
	'profile'		=> 'Личный кабинет',
	'login'			=> 'Авторизация',
	'registration'	=> 'Регистрация',
	'remind'		=> 'Восстановление пароля',
	'subscribe'		=> 'Подписка'
);

$a18n['menu2'] = 'меню 2';

if ($get['u']=='form') {
	if (empty($post['module'])) $post['module'] = 'pages';
	foreach ($modules_site as $k=>$v)
		if (!file_exists(ROOT_DIR.'modules/'.$k.'.php'))
			unset($modules_site[$k]);
}

$table = array(
	'_tree'		=> true,
	'_edit'		=> true,
	'id'		=> '',
	'name'		=> '',
	'title'		=> '',
	'url'		=> '',
	'module'	=> 'text',
	'menu'		=> 'boolean',
	'menu2'		=> 'boolean',
	'display'	=> 'display'
);

//только если многоязычный сайт
if ($config['multilingual']) {
	$languages = mysql_select("SELECT id,name FROM languages ORDER BY rank DESC", 'array');
	$get['language'] = (isset($_REQUEST['language']) && intval($_REQUEST['language'])) ? $_REQUEST['language'] : key($languages);
	if ($get['language'] == 0) $get['language'] = key($languages);
	$query = "
		SELECT pages.*
		FROM pages
		WHERE pages.language = '".$get['language']."'
	";
	$filter[] = array('language', $languages);
	$form[] = '<input name="language" type="hidden" value="'.$get['language'].'" />';
}

$delete['confirm'] = array('pages'=>'parent');

$form[] = array('input td7','name',true);
$form[] = array('select td3','module',array(true,$modules_site),array('help'=>'Модуль отвечает за тип информации на странице. Например, на странице модуля &quot;Новости&quot; будет отображатся список новостей.'));
$form[] = array('checkbox','display',true);
$form[] = array('parent td3 td4','parent',true);
$form[] = array('checkbox','menu',true);
$form[] = array('checkbox','menu2',true,array('help'=>'второе меню, обычно отображается в подвале сайта'));
$form[] = array('tinymce td12','text',true);//,array('attr'=>'style="height:500px"'));
$form[] = array('seo','seo url title keywords description',true);

?>
