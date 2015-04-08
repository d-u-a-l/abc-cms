Чтобы включить мультиязычность, нужно в файле /admin/config.php поставить значение
<br>
<pre>$config['multilingual'] = true;</pre>
<br>
В CMS реализовано два вида мультиязычности
<br>
<a class="label label-danger" data-toggle="collapse" href="#delete">Независимый</a><br>
<div id="delete" class="panel-collapse collapse bg-info">
	Языки не зависят друг от друга, в таблице есть колонка language в которой указан ИД языка.
	<br>Запись создается для одного языка и в каждом языке может быть разное количество записей.
	<br>Такой способ уже реализован в дереве сайта <code>/admin/modules/pages.php</code>.
	<br>В модуле админки нужно обязательно добавить фильтр по языку и скрытое поле языка в форму
<pre>//только если многоязычный сайт
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
}</pre>
	В админке в модуле словарь нужно добавить запрет на удаление языка если у него есть страницы
	<pre>$delete['confirm'] = array('pages'=>'language');</pre>
	На сайте в запросы добавляем ИД языка
	<pre>SELECT * FROM pages WHERE languge={$lang['id']}</pre>
</div>

<a class="label label-primary" data-toggle="collapse" href="#edit">Зеркальный</a><br>
<div id="edit" class="panel-collapse collapse bg-info">
	Языки зависят друг от друга. В таблице создаются копии колонок name1,name2 для разных языков.
	При создании нового языка в словаре нужно во все таблицы добавлять нужные колонки с индексами.
<pre>if ($get['u']=='edit') {
	if ($config['multilingual']) {
		if ($get['id'] == 'new') {
			$max = mysql_select("SELECT id FROM languages ORDER BY id DESC LIMIT 1",'string');
			$get['id'] = mysql_fn('insert', $get['m'], $post);
			mysql_query("ALTER TABLE `shop_products` ADD `name".$get['id']."` VARCHAR( 255 ) NOT NULL AFTER `name".$max."`");
			mysql_query("ALTER TABLE `shop_products` ADD `text".$get['id']."` TEXT NOT NULL AFTER `text".$max."`");
		}
	}
}</pre>
	В самом модуле админки с зеркальной многоязычностью нужно добавить вкладки языков для редактирования
	<br>Вкладку с основным языком отдельно мы не показываем, так как тексты для основного языка будут находится во вкладке с общими параметрами (категория, цена и т.д.)
	<br>Потому при редактировании формы мы перезаписываем тексты для основного языка
<pre>if ($config['multilingual']) {
	$config['languages'] = mysql_select("SELECT id,name FROM languages ORDEr BY display DESC, rank DESC",'rows');
	if ($get['u']=='edit') {
		//перезапись названия в основной язык
		$k = $config['languages'][0]['id'];
		$post['name'.$k] = $post['name'];
		$post['text'.$k] = $post['text'];
	}
	//вкладку с главным языком не показываем
	foreach ($config['languages'] as $k => $v) if ($k>0) {
		//вкладки
		$tabs['1' . $v['id']] = $v['name'];
		//поля
		$form['1' . $v['id']][] = array('input td12', 'name' . $v['id'], @$post['name' . $v['id']], array('name' => $a18n['name']));
		$form['1' . $v['id']][] = array('tinymce td12', 'text' . $v['id'], @$post['text' . $v['id']], array('name' => $a18n['text']));
	}
}</pre>

	<br>На сайте в sql запросы добавлять алиасы
	<pre>SELECT *,name{$lang['id']} as name, text{$lang['id']} as text FROM shop_products</pre>
</div>

<br>Чтобы многоязычность заработала на сайте, нужно сделать несколько вещей
<br>1. Раскомментировать строку в .htaccess, чтобы у нас была переменная <code>$u[0]</code>
<pre>RewriteRule ^([^/]*)/?([^/]*)/?([^/]*)/?([^/]*)/?$ index.php?u[0]=$1&u[1]=$2&u[2]=$3&u[3]=$4&u[5]=$5 [L,QSA]</pre>
2. В основном файле index.php инициализировать массив $lang
<pre>$lang = lang($u[0],'url');</pre>
3. В ссылки на страницы добавлять $u[0]
<pre>href="/{$lang['url']}/{$q['url']}/"</pre>