<?php
if (is_array($q)) {	?>	<ul class="message">
		<?php foreach ($q as $k=>$v) echo html_array('form/message',$v)?>
	</ul>
	<?php
}else {	?>	<li><?=$q?></li>
	<?php
}
?>
