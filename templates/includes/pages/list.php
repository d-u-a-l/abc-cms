<?php if ($i==1) { ?>
<ul class="pages_list">
<?php } ?>
	<li><a href="/<?=$q['url']?>/">&ndash; <?=$q['name']?></a></li>
<?php if ($i==$num_rows) { ?>
</ul>
<?php } ?>