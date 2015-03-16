<?php
	$nbsp = '';
	for ($i = 1; $i<=$q['level']; $i++) {
		$nbsp.= ' &nbsp;';
	}
?>
	<div>
		<?=$nbsp?> :.. <a href="/<?=$q['url']?>/"><?=$q['name']?></a>
	</div>