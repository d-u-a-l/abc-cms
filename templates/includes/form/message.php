<?php
if (is_array($q)) {
		<?php foreach ($q as $k=>$v) echo html_array('form/message',$v)?>
	</ul>
	<?php
}
	<?php
}
?>