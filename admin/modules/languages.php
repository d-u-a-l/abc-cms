<?php
$locales = array(
	'en'	=>	'Английский',
	'ar'	=>	'Арабский',
	'bg'	=>	'Болгарский',
	'ca'	=>	'Каталанский',
	'cn'	=>	'Китайский',
	'cs'	=>	'Чешский',
	'da'	=>	'Датский',
	'de'	=>	'Немецкий',
	'el'	=>	'Греческий',
	'es'	=>	'Испанский',
	'eu'	=>	'Баскский',
	'fa'	=>	'Фарси',
	'fi'	=>	'Финский',
	'fr'	=>	'Французский',
	'he'	=>	'Иврит',
	'hu'	=>	'Венгерский',
	'it'	=>	'Итальянский',
	'ja'	=>	'Японский',
	'kk'	=>	'Казахский',
	'lt'	=>	'Литовский',
	'lv'	=>	'Латышский',
	'nl'	=>	'Голландский',
	'no'	=>	'Норвежский',
	'pl'	=>	'Польский',
	'ptbr'	=>	'Португальский (Бразилия)',
	'ptpt'	=>	'Португальский',
	'ro'	=>	'Румынский',
	'ru'	=>	'Русский',
	'si'	=>	'Словенский',
	'sk'	=>	'Словацкий',
	'sl'	=>	'Словенский',
	'sr'	=>	'Сербский',
	'th'	=>	'Таиландский',
	'tr'	=>	'Турецкий',
	'tw'	=>	'Тайванский',
	'ua'	=>	'Украинский',
	'vi'	=>	'Вьетнамский',
);

//многоязычный
if ($config['multilingual']) {
	$table = array(
		'id'			=>	'rank:desc name id',
		'name'			=>	'',
		'rank'			=>	'',
		'url'			=>	'',
		'localization'	=>	$locales,
		'display'		=>	'display'
	);
	$form[0][] = array('input td4','name',true);
	$form[0][] = array('input td2','rank',true);
	$form[0][] = array('input td2','url',true);
	$form[0][] = array('select td2','localization',array(true,$locales));
	$form[0][] = array('checkbox td2','display',true);
}
//одноязычный
else {
	$pattern = 'one_form';
	$get['id'] = 1;
	if ($get['u']!='edit') {
		$post = mysql_select("
			SELECT *
			FROM languages
			WHERE id = 1
			LIMIT 1
		",'row');
	}
}

$a18n['localization'] = 'localization';

//исключения
if ($get['u']=='edit') {
	//$post['dictionary'] = serialize($post['dictionary']);
	if ($get['id'] > 0 AND (is_dir(ROOT_DIR . 'files/languages/' . $get['id'] . '/dictionary') || mkdir(ROOT_DIR . 'files/languages/' . $get['id'] . '/dictionary', 0755, true))) {
		foreach ($post['dictionary'] as $key => $val) {
			$str = '<?php' . PHP_EOL;
			$str .= '$lang[\'' . $key . '\'] = array(' . PHP_EOL;
			foreach ($val as $k => $v) {
				$str .= "	'" . $k . "'=>'" . str_replace("'", "\'", $v) . "'," . PHP_EOL;
			}
			$str .= ');';
			$str .= '?>';
			$fp = fopen(ROOT_DIR . 'files/languages/' . $get['id'] . '/dictionary/' . $key . '.php', 'w');
			fwrite($fp, $str);
			fclose($fp);
		}
	}
	unset($post['dictionary']);
	//если мультиязычный то нужно добавлять колонки в мультиязычные таблицы
	if ($config['multilingual']) {
		if ($get['id'] == 'new') {
			$max = mysql_select("SELECT id FROM languages ORDER BY id DESC LIMIT 1",'string');
			$get['id'] = mysql_fn('insert', $get['m'], $post);
			mysql_query("ALTER TABLE `shop_products` ADD `name".$get['id']."` VARCHAR( 255 ) NOT NULL AFTER `name".$max."`");
			mysql_query("ALTER TABLE `shop_products` ADD `text".$get['id']."` TEXT NOT NULL AFTER `text".$max."`");
		}
	}
}
else {
	//$dictionary = unserialize(@$post['dictionary']);
	$root = ROOT_DIR.'files/languages/'.$get['id'].'/dictionary';
	if (is_dir($root) && $handle = opendir($root)) {
		while (false !== ($file = readdir($handle))) {
			if (strlen($file)>2)
				include(ROOT_DIR.'files/languages/'.$get['id'].'/dictionary/'.$file);
		}
	}
}

//правила удаления для многоязычного
$delete['confirm'] = array('pages'=>'language');
$delete['delete'] = array(
	"ALTER TABLE `shop_products` DROP `name".$get['id']."`",
	"ALTER TABLE `shop_products` DROP `text".$get['id']."`",
);


//вкладки
$tabs = array(
	0 => 'Общее',
	1 => 'Формы',
	2 => 'Профайл',
	3 => 'Каталог',
	4 => 'Корзина',
	5 => 'Яндекс-маркет',
	6 => 'Рассылка'
);

$form[0][] = lang_form('input td12','common|site_name','название сайта');
$form[0][] = lang_form('textarea td12','common|txt_meta','metatag');
$form[0][] = lang_form('textarea td12','common|txt_head','текст в шапке');
$form[0][] = lang_form('textarea td12','common|txt_index','текст на главной');
$form[0][] = lang_form('textarea td12','common|txt_footer','текст в подвале');
$form[0][] = lang_form('input td12','common|str_no_page_name','название страницы 404');
$form[0][] = lang_form('textarea td12','common|txt_no_page_text','текст страницы 404');
$form[0][] = lang_form('input td12','common|wrd_more','подробнее');
$form[0][] = lang_form('input td12','common|msg_no_results','нет результатов');
$form[0][] = lang_form('input td12','common|wrd_no_photo','нет картинки');
$form[0][] = lang_form('input td8','common|breadcrumb_index','хлебные крошки: на главную');
$form[0][] = lang_form('input td4','common|breadcrumb_separator','хлебные крошки: разделитель');
$form[0][] = lang_form('input td4','common|make_selection','сделайте выбор');

$form[1][] = '<h2>Форма обратной связи</h2>';
$form[1][] = lang_form('input td12','feedback|name','имя');
$form[1][] = lang_form('input td12','feedback|email','еmail');
$form[1][] = lang_form('input td12','feedback|text','сообщение');
$form[1][] = lang_form('input td12','feedback|send','отправить');
$form[1][] = lang_form('input td12','feedback|attach','прикрепить файл');
$form[1][] = lang_form('input td12','feedback|message_is_sent','сообщение отправлено');
$form[1][] = '<h2>Сообщения в формах</h2>';
$form[1][] = lang_form('input td12','validate|no_required_fields','не заполнены обязательные поля');
$form[1][] = lang_form('input td12','validate|short_login','короткий логин');
$form[1][] = lang_form('input td12','validate|not_valid_login','некорректный логин');
$form[1][] = lang_form('input td12','validate|not_valid_email','некорректный email');
$form[1][] = lang_form('input td12','validate|not_valid_password','некорректный пароль');
$form[1][] = lang_form('input td12','validate|not_valid_captcha','некорректный защитный код');
$form[1][] = lang_form('input td12','validate|not_valid_captcha2','отключены скрипты');
$form[1][] = lang_form('input td12','validate|error_email','ошибка при отправке письма');
$form[1][] = lang_form('input td12','validate|no_email','в базе нету такого email');
$form[1][] = lang_form('input td12','validate|duplicate_login','дублирование логина');
$form[1][] = lang_form('input td12','validate|duplicate_email','дублирование email');
$form[1][] = lang_form('input td12','validate|not_match_passwords','пароли не совпадают');

$form[2][] = lang_form('input td12','profile|hello','здравствуйте');
$form[2][] = lang_form('input td12','profile|link','личный кабинет');
$form[2][] = lang_form('input td12','profile|user_edit','личные данные');
$form[2][] = lang_form('input td12','profile|exit','выйти');
$form[2][] = '<h2>Форма авторизации/регистрации/редактирования</h2>';
$form[2][] = lang_form('input td3','profile|email','еmail');
$form[2][] = lang_form('input td3','profile|password','пароль');
$form[2][] = lang_form('input td3','profile|password2','подтв. пароль');
$form[2][] = lang_form('input td3','profile|new_password','новый пароль');
$form[2][] = lang_form('input td3','profile|save','сохранить');
$form[2][] = lang_form('input td3','profile|registration','регистрация');
$form[2][] = lang_form('input td3','profile|enter','войти');
$form[2][] = lang_form('input td3','profile|remember_me','запомнить меня');
$form[2][] = lang_form('input td3','profile|auth','авторизация');
$form[2][] = lang_form('input td3','profile|remind','забыли пароль');
$form[2][] = lang_form('input td12','profile|successful_registration','успешная регистрация');
$form[2][] = lang_form('input td12','profile|successful_auth','успешная авторизация');
$form[2][] = lang_form('input td12','profile|error_auth','ошибка авторизации');
$form[2][] = lang_form('input td12','profile|msg_exit','Вы вышли!');
$form[2][] = lang_form('input td12','profile|go_to_profile','перейти в профиль');
$form[2][] = '<h2>Восстановление пароля</h2>';
$form[2][] = lang_form('input td12','profile|remind_button','отправить письмо по восстановлению пароля');
$form[2][] = lang_form('input td12','profile|successful_remind','отправлено письмо по восстановлению пароля');

$form[3][] = lang_form('input td3','shop|catalog','каталог');
$form[3][] = lang_form('input td3','shop|new','новинки');
$form[3][] = lang_form('input td3','shop|brand','производитель');
$form[3][] = lang_form('input td3','shop|article','артикул');
$form[3][] = lang_form('input td3','shop|parameters','параметры');
$form[3][] = lang_form('input td3','shop|price','цена');
$form[3][] = lang_form('input td3','shop|currency','валюта');
$form[3][] = lang_form('input td3','shop|product_random','случайный товар');
$form[3][] = lang_form('input td3','shop|filter_button','искать');
$form[3][] = '<h2>Отзывы</h2>';
$form[3][] = lang_form('input td3','shop|reviews','Отзывы');
$form[3][] = lang_form('input td3','shop|review_add','Оставить отзыв');
$form[3][] = lang_form('input td3','shop|review_name','имя');
$form[3][] = lang_form('input td3','shop|review_email','еmail');
$form[3][] = lang_form('input td3','shop|review_text','сообщение');
$form[3][] = lang_form('input td3','shop|review_send','отправить');
$form[3][] = lang_form('input td12','shop|review_is_sent','отзыв добавлен');

$form[4][] = lang_form('input td3','basket|buy','купить');
$form[4][] = lang_form('input td3','basket|basket','корзина');
$form[4][] = lang_form('input td12','basket|empty','пустая корзина');
$form[4][] = lang_form('input td12','basket|go_basket','перейти в корзину');
$form[4][] = lang_form('input td12','basket|go_next','продолжить покупки');
$form[4][] = lang_form('input td12','basket|product_added','товар добавлен');
$form[4][] = '<h2>Оплата</h2>';
$form[4][] = lang_form('input td12','order|payments','оплата');
$form[4][] = lang_form('input td12','order|pay','оплатить');
$form[4][] = lang_form('input td12','order|paid','оплачен');
$form[4][] = lang_form('input td12','order|not_paid','не плачен');
$form[4][] = lang_form('textarea td12','order|success','успешная оплата');
$form[4][] = lang_form('textarea td12','order|fail','отказ оплаты');

$form[4][] = '<h2>Таблица товаров</h2>';
$form[4][] = lang_form('input td3','basket|product_id','id товара');
$form[4][] = lang_form('input td3','basket|product_name','название товара');
$form[4][] = lang_form('input td3','basket|product_price','цена');
$form[4][] = lang_form('input td3','basket|product_count','количество');
$form[4][] = lang_form('input td3','basket|product_summ','сумма');
$form[4][] = lang_form('input td3','basket|product_cost','стоимость');
$form[4][] = lang_form('input td3','basket|product_delete','удалить');
$form[4][] = lang_form('input td3','basket|total','итого');
$form[4][] = '<h2>Параметры заказа</h2>';
$form[4][] = lang_form('input td3','basket|profile','личные данные');
$form[4][] = lang_form('input td3','basket|delivery','доставка');
$form[4][] = lang_form('input td3','basket|delivery_cost','стоимость доставки');
$form[4][] = lang_form('input td3','basket|comment','коммен к заказу');
$form[4][] = lang_form('input td3','basket|order','оформить заказ');
$form[4][] = '<h2>Статистика заказов</h2>';
$form[4][] = lang_form('input td3','basket|orders','статистика заказов');
$form[4][] = lang_form('input td3','basket|order_name','заказ');
$form[4][] = lang_form('input td3','basket|order_from','от');
$form[4][] = lang_form('input td3','basket|order_status','статус');
$form[4][] = lang_form('input td3','basket|order_date','дата');
$form[4][] = lang_form('input td3','basket|view_order','просмотр заказа');

$form[5][] = 'Полное описание можно найти на странице <a target="_balnk" href="http://help.yandex.ru/partnermarket/shop.xml">http://help.yandex.ru/partnermarket/shop.xml</a><br /><br />';
$form[5][] = lang_form('input td12','market|name','Короткое название магазина');
$form[5][] = lang_form('input td12','market|company','Полное наименование компании');
$form[5][] = lang_form('input td12','market|currency','Валюта магазина');

$form[6][] = '<h2>Основной шаблон автоматического письма</h2>';
$form[6][] = lang_form('textarea td12','common|letter_top','Текст в шапке письма');
$form[6][] = lang_form('textarea td12','common|letter_footer','Текст в подвале письма');
$form[6][] = '<h2>Основной шаблон письма рассылки</h2>';
$form[6][] = lang_form('textarea td12','subscribe|top','Текст в шапке рассылки');
$form[6][] = lang_form('textarea td12','subscribe|bottom','Текст в подвале рассылки');
$form[6][] = lang_form('input td8','subscribe|letter_failure_str','Если вы хотите отписаться от рассылки нажмите на');
$form[6][] = lang_form('input td4','subscribe|letter_failure_link','ссылку');
$form[6][] = '<h2>Подписка</h2>';
$form[6][] = lang_form('input td12','subscribe|on_button','Подписаться');
$form[6][] = lang_form('input td12','subscribe|on_success','Вы успешно подписаны');
$form[6][] = lang_form('input td12','subscribe|failure_text','Подтвердите, что хотите отписаться');
$form[6][] = lang_form('input td12','subscribe|failure_button','Отписаться');
$form[6][] = lang_form('input td12','subscribe|failure_success','Вы отписаны');

function lang_form($type,$key,$name) {
	global $lang;
	$key = explode('|',$key);
	return array ($type,'dictionary['.$key[0].']['.$key[1].']',isset($lang[$key[0]][$key[1]]) ? $lang[$key[0]][$key[1]] : '',array('name'=>$name.' <b>'.$key[0].'|'.$key[1].'</b>','title'=>$key[0].'|'.$key[1]));
}


/*
$form[0][] = lang_form('input td12','site_name','название сайта');
$form[0][] = lang_form('textarea td12','txt_meta','metatag');
$form[0][] = lang_form('textarea td12','txt_head','текст в шапке');
$form[0][] = lang_form('textarea td12','txt_index','текст на главной');
$form[0][] = lang_form('textarea td12','txt_footer','текст в подвале');
$form[0][] = lang_form('input td12','str_no_page_name','название страницы 404');
$form[0][] = lang_form('textarea td12','txt_no_page_text','текст страницы 404');
$form[0][] = lang_form('input td12','wrd_more','подробнее');
$form[0][] = lang_form('input td12','msg_no_results','нет результатов');
$form[0][] = lang_form('input td12','wrd_no_photo','нет картинки');
$form[0][] = lang_form('input td8','breadcrumb_index','хлебные крошки: на главную');
$form[0][] = lang_form('input td4','breadcrumb_separator','хлебные крошки: разделитель');
$form[0][] = lang_form('input td4','make_selection','сделайте выбор');

$form[1][] = '<h2>Форма обратной связи</h2>';
$form[1][] = lang_form('input td12','feedback_name','имя');
$form[1][] = lang_form('input td12','feedback_email','еmail');
$form[1][] = lang_form('input td12','feedback_text','сообщение');
$form[1][] = lang_form('input td12','feedback_send','отправить');
$form[1][] = lang_form('input td12','feedback_attach','прикрепить файл');
$form[1][] = lang_form('input td12','feedback_message_is_sent','сообщение отправлено');

$form[1][] = '<h2>Сообщения в формах</h2>';
$form[1][] = lang_form('input td12','msg_no_required_fields','не заполнены обязательные поля');
$form[1][] = lang_form('input td12','msg_short_login','короткий логин');
$form[1][] = lang_form('input td12','msg_not_valid_login','некорректный логин');
$form[1][] = lang_form('input td12','msg_not_valid_email','некорректный email');
$form[1][] = lang_form('input td12','msg_not_valid_password','некорректный пароль');
$form[1][] = lang_form('input td12','msg_not_valid_captcha','некорректный защитный код');
$form[1][] = lang_form('input td12','msg_not_valid_captcha2','отключены скрипты');
$form[1][] = lang_form('input td12','msg_error_email','ошибка при отправке письма');
$form[1][] = lang_form('input td12','msg_no_email','в базе нету такого email');
$form[1][] = lang_form('input td12','msg_duplicate_login','дублирование логина');
$form[1][] = lang_form('input td12','msg_duplicate_email','дублирование email');
$form[1][] = lang_form('input td12','msg_not_match_passwords','пароли не совпадают');

$form[2][] = lang_form('input td12','profile_hello','здравствуйте');
$form[2][] = lang_form('input td12','profile_link','личный кабинет');
$form[2][] = lang_form('input td12','profile_user_edit','личные данные');
$form[2][] = lang_form('input td12','profile_exit','выйти');
$form[2][] = '<h2>Форма авторизации/регистрации/редактирования</h2>';
$form[2][] = lang_form('input td3','profile_email','еmail');
$form[2][] = lang_form('input td3','profile_password','пароль');
$form[2][] = lang_form('input td3','profile_password2','подтв. пароль');
$form[2][] = lang_form('input td3','profile_new_password','новый пароль');
$form[2][] = lang_form('input td3','profile_save','сохранить');
$form[2][] = lang_form('input td3','profile_registration','регистрация');
$form[2][] = lang_form('input td3','profile_enter','войти');
$form[2][] = lang_form('input td3','profile_remember_me','запомнить меня');
$form[2][] = lang_form('input td3','profile_auth','авторизация');
$form[2][] = lang_form('input td3','profile_remind','забыли пароль');
$form[2][] = lang_form('input td12','profile_successful_registration','успешная регистрация');
$form[2][] = lang_form('input td12','profile_successful_auth','успешная авторизация');
$form[2][] = lang_form('input td12','profile_error_auth','ошибка авторизации');
$form[2][] = lang_form('input td12','profile_msg_exit','Вы вышли!');
$form[2][] = lang_form('input td12','profile_go_to_profile','перейти в профиль');
$form[2][] = '<h2>Восстановление пароля</h2>';
$form[2][] = lang_form('input td12','profile_remind_button','отправить письмо по восстановлению пароля');
$form[2][] = lang_form('input td12','profile_successful_remind','отправлено письмо по восстановлению пароля');

$form[3][] = lang_form('input td3','shop_catalog','каталог');
$form[3][] = lang_form('input td3','shop_new','новинки');
$form[3][] = lang_form('input td3','shop_brand','производитель');
$form[3][] = lang_form('input td3','shop_article','артикул');
$form[3][] = lang_form('input td3','shop_parameters','параметры');
$form[3][] = lang_form('input td3','shop_product_random','случайный товар');
$form[3][] = lang_form('input td3','shop_currency','валюта');
$form[3][] = lang_form('input td3','shop_filter_button','искать');
$form[3][] = '<h2>Отзывы</h2>';
$form[3][] = lang_form('input td3','reviews','Отзывы');
$form[3][] = lang_form('input td3','review_add','Оставить отзыв');
$form[3][] = lang_form('input td3','review_name','имя');
$form[3][] = lang_form('input td3','review_email','еmail');
$form[3][] = lang_form('input td3','review_text','сообщение');
$form[3][] = lang_form('input td3','review_send','отправить');
$form[3][] = lang_form('input td12','review_is_sent','отзыв добавлен');


$form[4][] = lang_form('input td3','basket_buy','купить');
$form[4][] = lang_form('input td3','basket','корзина');
$form[4][] = lang_form('input td12','basket_empty','пустая корзина');
$form[4][] = lang_form('input td12','basket_go_basket','перейти в корзину');
$form[4][] = lang_form('input td12','basket_go_next','продолжить покупки');
$form[4][] = lang_form('input td12','basket_product_added','товар добавлен');
$form[4][] = '<h2>Таблица товаров</h2>';
$form[4][] = lang_form('input td3','basket_product_id','id товара');
$form[4][] = lang_form('input td3','basket_product_name','название товара');
$form[4][] = lang_form('input td3','basket_product_price','цена');
$form[4][] = lang_form('input td3','basket_product_count','количество');
$form[4][] = lang_form('input td3','basket_product_summ','сумма');
$form[4][] = lang_form('input td3','basket_product_cost','стоимость');
$form[4][] = lang_form('input td3','basket_product_delete','удалить');
$form[4][] = lang_form('input td3','basket_total','итого');
$form[4][] = '<h2>Параметры заказа</h2>';
$form[4][] = lang_form('input td3','basket_profile','личные данные');
$form[4][] = lang_form('input td3','basket_delivery','доставка');
$form[4][] = lang_form('input td3','basket_delivery_cost','стоимость доставки');
$form[4][] = lang_form('input td3','basket_comment','коммен к заказу');
$form[4][] = lang_form('input td3','basket_order','оформить заказ');
$form[4][] = '<h2>Статистика заказов</h2>';
$form[4][] = lang_form('input td3','basket_orders','статистика заказов');
$form[4][] = lang_form('input td3','basket_order_name','заказ');
$form[4][] = lang_form('input td3','basket_order_from','от');
$form[4][] = lang_form('input td3','basket_order_status','статус');
$form[4][] = lang_form('input td3','basket_order_date','дата');
$form[4][] = lang_form('input td3','basket_view_order','просмотр заказа');

$form[5][] = 'Полное описание можно найти на странице <a target="_balnk" href="http://help.yandex.ru/partnermarket/shop.xml">http://help.yandex.ru/partnermarket/shop.xml</a><br /><br />';
$form[5][] = lang_form('input td12','market_name','Короткое название магазина');
$form[5][] = lang_form('input td12','market_company','Полное наименование компании');
$form[5][] = lang_form('input td12','market_currency','Валюта магазина');

$form[6][] = '<h2>Основной шаблон автоматического письма</h2>';
$form[6][] = lang_form('input td12','letter_top','Текст в шапке письма');
$form[6][] = lang_form('input td12','letter_footer','Текст в подвале письма');
$form[6][] = '<h2>Подписка</h2>';
$form[6][] = lang_form('input td12','subscribe_top','Текст в шапке рассылки');
$form[6][] = lang_form('input td12','subscribe_bottom','Текст в подвале рассылки');
$form[6][] = lang_form('input td12','subscribe_letter_failure_str','Если вы хотите отписаться от рассылки нажмите на');
$form[6][] = lang_form('input td12','subscribe_letter_failure_link','ссылку');
$form[6][] = lang_form('input td12','subscribe_on_button','Подписаться');
$form[6][] = lang_form('input td12','subscribe_on_success','Вы успешно подписаны');
$form[6][] = lang_form('input td12','subscribe_on_letter_name','Подписан новый пользователь');
$form[6][] = lang_form('input td12','subscribe_failure_text','Подтвердите, что хотите отписаться');
$form[6][] = lang_form('input td12','subscribe_failure_button','Отписаться');
$form[6][] = lang_form('input td12','subscribe_failure_success','Вы отписаны');

function lang_form($type,$key,$name) {
	global $dictionary;
	return array ($type,'dictionary['.$key.']',isset($dictionary[$key]) ? $dictionary[$key] : '',array('name'=>$name.' <b>'.$key.'</b>','title'=>$key));
}
*/
?>