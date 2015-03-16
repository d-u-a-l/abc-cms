<div class="textarea <?=isset($q['class']) ? $q['class'] : ''?>">
	<?php
	if (isset($q['caption'])) {		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
	}
	?>
	<div class="data">
		<textarea name="<?=$q['name']?>" <?=isset($q['attr']) ? $q['attr'] : ''?>><?=isset($q['value']) ? $q['value'] : ''?></textarea>
	</div>
	<div class="clear"></div>
</div>
