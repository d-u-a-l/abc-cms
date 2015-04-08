<style>
.panel-collapse {padding:0 0 0 30px}
</style>

<span class="label label-default">_/</span> – полезные скрипты для инсталляции, обновления и исправления CMS<br>

<a class="label label-warning" data-toggle="collapse" href="#f_admin">admin/</a> – админпанель сайта<br>
<div id="f_admin" class="panel-collapse collapse">
	<span class="label label-warning">actions/</span> - скрипты действий ($_GET[u] = form|edit|post)
	<br>
	<span class="label label-warning">backup/</span> - дампы БД
	<br>
	<span class="label label-warning">modules/</span> - основные разделы админки ($_GET[m] = pages|news|config)
	<br>
	<span class="label label-warning">templates/</span> - нтмл код, скрипты и стили
	<br>
	<span class="label label-warning">config.php</span> – настройки админки
</div>
<span class="label label-default"">ajax/</span> – скрипты для аджакс запросов, например /ajax.php?file=captcha<br>
<span class="label label-default"">cron/</span> – скрипты для задач cron, например /cron.php?file=cbr<br>
<span class="label label-default"">files/</span> – файлы созданные через админпанель (картинки товаров и т.д.)<br>
<a class="label label-primary" data-toggle="collapse" href="#f_functions">functions/</a> – основной набор всех функций (ядро), которые используются на сайте<br>
<div id="f_functions" class="panel-collapse collapse">
	<span class="label label-warning">admin_func.php</span> – функции админпанели
	<br><span class="label label-primary">auth_func.php</span> – функции авторизации и прав доступа
	<br><span class="label label-primary">common_func.php</span> – общин функции
	<br><span class="label label-primary">file_func.php</span> – функции для работы с файлами и папками
	<br><span class="label label-primary">form_func.php</span> – функции для работы с формами
	<br><span class="label label-primary">html_func.php</span> – функции для работы с нтмл кодом
	<br><span class="label label-primary">image_func.php</span> – функции для работы с изображениями
	<br><span class="label label-primary">lang_func.php</span> – языковые функции
	<br><span class="label label-primary">mail_func.php</span> – функции для работы с почтой
	<br><span class="label label-primary">mysql_func.php</span> – функции для работы с MySQL
	<br><span class="label label-primary">string_func.php</span> – функции для работы со строками
</div>
<span class="label label-success">modules/</span> – основные модули сайта<br>
<a class="label label-info" data-toggle="collapse" href="#f_plugins">plugins/</a> – набор различных плагинов и фреймворков<br>
<div id="f_plugins" class="panel-collapse collapse">
	bootstrap
	<br>captha
	<br>CodeMirror – подсетка кода в редакторе
	<br>higslide – фотогалерея
	<br>jquery
	<br>phpexcel – работа с excel
	<br>robokassa
	<br>tinymce
</div>
<a class="label label-success" data-toggle="collapse" href="#f_templates">templates/</a> – весь html,javascript,css код и шрифты сайта<br>
<div id="f_plugins" class="panel-collapse collapse">
	css<br>
	fonts<br>
	images<br>
	includes<br>
	scripts<br>
	sprite
</div>
<span class="label label-primary">.htaccess</span> – настройка сервере, rewritemod и т.д.
<br><span class="label label-primary">_config.php</span> – динамические настройки сайта
<br><span class="label label-primary">_config2.php</span> – статические настройки сайта, php, mysql и обработчик ошибок
<br><span class="label label-warning">admin.php</span> – корневой файл админки
<br><span class="label label-default">ajax.php</span> – корневой файл аджакс запросов
<br><span class="label label-default">cron.php</span> – корневой файл задач крон
<br><span class="label label-success">index.php</span> – корневой файл сайта
<br><span class="label label-default">market.php</span> – выгрузка в Yandex Market
<br><span class="label label-default">robots.txt</span> – настройки для поисковиков
<br><span class="label label-default">sitemap.php</span> – генерация sitemap.php

