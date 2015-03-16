<?php
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);
define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'config_db.php');
?>
<style>
div {padding:5px}
.box {float:left}
h2 {padding:0; margin:0; font:bold 14px Arial}
table {width:250px; font:12px Arial; border-collapse: collapse;}
table td {vertical-align:top; padding:2px; border: 1px solid gray;}
</style>
<div>
<?php
$query = "SHOW TABLES";
$result = mysql_query($query);
$i=0;
while ($q = mysql_fetch_array($result)) {
	$query = "SHOW CREATE TABLE `".$q[0]."`";
	$result2 = mysql_query($query);
	$q = mysql_fetch_assoc($result2);
	$array = explode("\n",$q['Create Table']);
	$comment = end($array);
	$comment = stristr($comment,"COMMENT");
	$comment = substr($comment,9);
	$comment = substr($comment,0,-1);
	echo fmod($i,4)==0 ? '<div style="clear:both"></div>' : '';
	echo '<div class="box">';
	echo '<h2>'.$q['Table'].'</h2>';
	echo $comment.'<br />';
	echo '<table cellspacing="0" cellpadding="0">';
	echo '<tr>';
	echo '<td>название</td>';
	echo '<td>тип</td>';
	echo '<td>описание</td>';
	echo '</tr>';
	foreach ($array as $k=>$v) {		$v = trim($v);		if ($v[0]=='`') {
			$data = stristr($v,"`");
			$data = substr($data, 1);
			$name = func($data,'`',0);
			$data = stristr($data," ");
			$data = substr($data, 1);
			$type = func($data,' ',0);
			$data = stristr($data,'COMMENT');
			$data = substr($data,9);
			$comment = func($data,"'",0);
			echo '<tr>';
			echo '<td>'.$name.'</td>';
			echo '<td>'.$type.'</td>';
			echo '<td>'.$comment.'</td>';
			echo '</tr>';
		}
	}
	echo '</table>';
	echo '</div>';
	$i++;
}

function func ($data,$str,$i=0) {
	$count	= strpos($data,$str);
	return substr($data,0,$count+$i);
}
?>
</div>