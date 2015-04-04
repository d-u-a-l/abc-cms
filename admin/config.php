<?php

$config['cms_version'] = '1.0.3';

$config['multilingual'] = true; //многоязычный сайт

$config['merchants'] = array(
	1 => 'наличный рассчет',
	2 => 'robokassa [все платежи]',
	3 => 'robokassa [yandex]',
	4 => 'robokassa [wmr]',
	5 => 'robokassa [qiwi]',
	6 => 'robokassa [терминал]',
	7 => 'robokassa [банковской картой]',
);
$config['payments'] = array(
	1 => 'наличный рассчет',
	2 => 'robokassa',
);

$config['depend'] = array(
	//'shop_products'=>array('categories'=>'shop_products-categories'),
);

$config['boolean'] = array(
	'boolean','display','market','yandex_index',
);

$modules_admin = array(
	'pages'			=> 'pages',
	'news'		=> 'news',
	'gallery' => array(
		'gallery'	=> 'gallery',
		'slider'	=> 'slider',
	),
	'dictionary'	=> 'languages',
	'feedback'=>'feedback',

	'catalog' => array(
		'shop_products'	=> 'shop_products',
		'shop_categories'	=> 'shop_categories',
		'shop_brands'	=> 'shop_brands',
		'shop_parameters'	=> 'shop_parameters',
		'shop_reviews'	=> 'shop_reviews',
	),
	'synchronization'=>array(
		'export'	=> 'shop_export',
		'import'	=> 'shop_import',
	),
	'shop' => array(
		'orders'			=> 'orders',
		'order_types'		=> 'order_types',
		'order_deliveries'	=> 'order_deliveries',
		'order_payments'	=> 'order_payments',
	),

	'users' => array(
		'users'	=> 'users',
		'user_types'	=> 'user_types',
		'user_fields'	=> 'user_fields',
	),
	'subscribe'=>array(
		'subscribers'		=> 'subscribers',
		'subscribe_letters'	=> 'subscribe_letters',
		'letters'			=> 'letters',
	),

	'config' => array(
		'config'			=> 'config',
		'letter_templates'	=> 'letter_templates',
		'logs'				=> 'logs',
	),
	'design' => array(
		'template_css'	=> 'template_css',
		'template_images'	=> 'template_images',
		'template_includes'	=> 'template_includes',
		'template_scripts'	=> 'template_scripts'
	),
	'backup' => array(
		'backup'	=> 'backup',
		'restore'	=> 'restore'
	),
	'seo' => array(
		'redirects'		=> 'redirects',
		'robots.txt'	=> 'seo_robots',
		'sitemap.xml'	=> 'seo_sitemap',
		'.htaccess'		=> 'seo_htaccess',
		'links'		=> 'seo_links',
		'pages'		=> 'seo_pages',
		'import'		=> 'seo_links_import',
		'export'		=> 'seo_links_export',
	),
);
?>