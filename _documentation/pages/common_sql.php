Основная функция для выполнения select запросов - mysql_select()<br>
<pre>$data = mysql_select("SELECT * FROM news WHERE id=1",'row',3600);</pre>
<ol>
<li>sql запрос</li>
<li>тип возвращаемых данных<br>

<a class="label label-success" data-toggle="collapse" href="#string">string</a> - строка<br>
<div id="string" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT name FROM news WHERE id=1",'string')</pre>
	Вернет только значение одного поля name
	<pre>название новости</pre>
</div>

<a class="label label-default" data-toggle="collapse" href="#num_rows">num_rows</a> - количество записей<br>
<div id="num_rows" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id FROM news ",'num_rows')</pre>
	Вернет количество записей
	<pre>12</pre>
</div>

<a class="label label-warning" data-toggle="collapse" href="#row">row</a> - один ряд<br>
<div id="row" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT * FROM news WHERE id=1 LIMIT 1",'row')</pre>
	Вернет ряд с одной записью
<pre>array(
	'id'=>'12',
	'name'=>'Название',
	'text'=>'текст'
)</pre>
</div>

<a class="label label-primary" data-toggle="collapse" href="#rows">rows</a> - массив из row<br>
<div id="rows" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT * FROM news LIMIT 10",'rows')</pre>
	Вернет все выбранные ряды
<pre>array(
	array(
		'id'=>'12',
		'name'=>'Название',
		'text'=>'текст'
	),
	array(
		'id'=>'14',
		'name'=>'Название',
		'text'=>'текст'
	),
	array(
		'id'=>'17',
		'name'=>'Название',
		'text'=>'текст'
	)
)</pre>
</div>

<a class="label label-info" data-toggle="collapse" href="#rows_id">rows_id</a> массив из row где ключом будет id<br>
<div id="rows_id" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id FROM news",'rows_id')</pre>
	Вернет все выбранные ряды
<pre>array(
	12=>array(
		'id'=>'12',
		'name'=>'Название',
		'text'=>'текст'
	),
	14=>array(
		'id'=>'14',
		'name'=>'Название',
		'text'=>'текст'
	),
	17=>array(
		'id'=>'17',
		'name'=>'Название',
		'text'=>'текст'
	)
)</pre>
</div>

<a class="label label-danger" data-toggle="collapse" href="#array">array</a> - массив $k->$v - SELECT id,name .. FROM LIMIT 1 => array(1=>'значение',2=>'значение')<br>
<div id="array" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id,name FROM news",'array')</pre>
	Вернет простой массив
<pre>array(
	12=>'Название',
	14=>'Название',
	17=>'Название',
)</pre>
</div>
</li>
<li>время кеширования в секундах<br>
Если пусто или 0 то кеш не создается и запрос делается в базу<br>
Если значение больше 0 то текст запроса оборачивается в md5() и функция смотрит в папку cache есть ли там такой файл<br>
Если файл есть, то смотрит время создания файла<br>
Если время создания меньше времени кеша, то запрос не делается а используется файл кеша<br>
Если время создания больше времени кеша, то делается запрос в базу а файл кеша обновляется
<div class="bs-callout bs-callout-danger">
Кешировать нужно только самые частые и сложные запросы и только если начало расти количество посетителей
</div>
	</li>
</ol>