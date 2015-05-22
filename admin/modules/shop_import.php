<?php

$table = array(
	'A'=>'id',
	'B'=>'article',
	'C'=>'price',
	'D'=>'price2',
	'E'=>'brand',
	'F'=>'category',
	'G'=>'name',
	'J'=>'display',
	'H'=>'special',
	'K'=>'url',
	'L'=>'title',
	'M'=>'keywords',
	'N'=>'description',
	'O'=>'text',
);

$content = '<br /><h2>Загрузка файла excel (csv, xls, xlsx)</h2>';
$content.= '<form method="post" enctype="multipart/form-data" action="">';
$content.= '<br /><input type="file" name="i">';
$content.= '<input type="submit" name="upload" value="Загрузить файл">';
$content.= '</form>';

if (count($_POST)>0) {
	$file = $exc = $data = false;
	//загрузка файла
	if (isset($_POST['upload'])) {
		if (is_file($_FILES["i"]["tmp_name"])) {
			$file = strtolower(trunslit($_FILES['i']['name']));
			$exc = end(explode('.',$file));
			$file = ROOT_DIR."files/temp/".$file; //die($file);
			if (is_dir(ROOT_DIR.'files/temp') || mkdir(ROOT_DIR.'files/temp',0755,true)) {
				copy($_FILES["i"]["tmp_name"],$file);
			}
		}
		else $content.= '<br /><b>ошибка загрузки файла</b>';
	}
	//импорт файла
	elseif (isset($_POST['import'])) {
		if (is_file($_POST['file'])) {
			$file = $_POST['file'];
			$exc = end(explode('.',$file));
		}
		else {
			$content.= '<br /><b>ошибка загрузки файла</b>';
		}
	}
	//обработка файла
	if ($file AND is_file($file)) {
		//загрузка csv
		$i = 0;
		if ($exc=='csv') {
			$handle = fopen($file, 'r');
			while (($value = fgetcsv($handle, 8000, ';')) !== FALSE) {
				$i++;
				foreach ($table as $k=>$v) {
					if ($k=='A') {
						$data[$i][$k] = iconv("cp1251", "UTF-8",current($value));
						//next($value);
					}
					else $data[$i][$k] = iconv("cp1251", "UTF-8",next($value));
				}
			}
			fclose($handle);
		}
		//загрузка excel
		elseif ($exc=='xls' OR $exc=='xlsx') {
			include ROOT_DIR.'plugins/phpexcel/PHPExcel/IOFactory.php';
			$inputFileName = $file;
			//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$data = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		}
	}
	else {
		$content.= '<br /><b>ошибка типа файла</b>';
	}
	//вывод данных на экран
	if (is_array($data)) { //print_r($data);
		if (isset($_POST['upload'])) {
			$content = '<br /><h2>Подтверждение загрузки</h2>';
			$content.= '<form method="post" action=""><input name="file" type="hidden" value="'.$file.'"><input type="submit" name="import" value="Загрузить данные на сайт?"> &nbsp; <a href="">назад</a></form><br />';
			$content.= '<br /><h2>Содержание загруженого файла</h2>';
			$content.= 'Товары на зеленом фоне будут обновлены, товары на красном фоне будут добавлены<br /><br />';
			$content.= '<table class="table"><tr>';
			foreach ($table as $k=>$v) {
				$content.= '<th>'.$a18n[$v].'</th>';
			}
		}
		else {
			$insert = $update = 0;
			$content = '<h3>Результаты загрузки</h3>';
		}
		foreach ($data as $key=>$value) {
			if ($value['A']>0) {
				$num_rows = mysql_select("SELECT * FROM shop_products WHERE id = '".$value['A']."'",'num_rows');
				$color = $num_rows==0 ? 'darkred' : 'green';
				if (isset($_POST['upload'])) $content.= '<tr class="bg_'.$color.'">';
				$post = array();
				foreach ($table as $k=>$v) {
					if (isset($_POST['upload'])) {
						$content.= '<td>'.$value[$k].'</td>';
					}
					//формирование массива для обновления БД
					else {
						$post[$v] = $value[$k];
					}
				}
				if (isset($_POST['import'])) {
					if ($num_rows!=1) {
						mysql_fn('insert','shop_products',$post);
						$insert++;
					}
					else {
						mysql_fn('update','shop_products',$post);
						$update++;
					}
				}
				if (isset($_POST['upload']))  $content.= '</tr>';
			}
		}
		if (isset($_POST['upload'])) $content.= '</table>';
		else {
			$content.= '<br />Количество обновленных товаров:'.$update;
			$content.= '<br />Количество добавленных товаров:'.$insert;
		}
	}
	else {
		$content.= '<br /><b>ошибка обработки файла</b>';
	}
}
unset($table);
