Админпанель состоит из разделов (модулей)<br>
В каждом разделе отображается список записей в виде таблицы<br>
Каждый ряд можно открыть для редактирования - всплывет форма<br>
<br>
<strong>Логика работы</strong><br>
принимающий скрипт /admin.php<br>
передается два основных параметра<br>
$_GET['m'] - модуль /admin/modules/***.php<br>
$_GET['u'] - операция /admin/аctions/***.php<br>

<br>
<strong>Модули</strong><br>
Модули находятся по адресу /admin/modules/***.php<br>
Обычно один модуль отвечает за редактирование данных в одноименной таблице в базе данных<br>
