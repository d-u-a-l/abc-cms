Сайт состоит из модулей (новости, каталог, обратная связь, корзина, текстовая страница и т.д.)<br>
В каждом модуле идут запросы к базе данных для необходимых данных и подключение шаблонов для отображения этих данных<br>

<br>
<strong>Логика работы</strong><br>
принимающий скрипт /index.php<br>
передается массив $_GET['u'], в котором значения идут по порядку 1,2,3,4,5<br>
так урл /shop/notebooks/acer/ будет разбит на три части
<pre>$u[1] = 'shop';
$u[2] = 'notebooks';
$u[2] = 'acer';</pre>
Это происходит благодяря правилу rewrite в .htaccess
<pre>RewriteRule ^([^/]*)/?([^/]*)/?([^/]*)/?([^/]*)/?$ index.php?u[1]=$1&u[2]=$2&u[3]=$3&u[4]=$4 [L,QSA]</pre>

Дальше мы всегда делаем запрос к основной таблице pages
<pre>SELECT * FROM pages WHERE url=$u[1]</pre>
где в поле module у нас находится название модуля /modules/***.php
