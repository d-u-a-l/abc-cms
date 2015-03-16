<div class="input <?=isset($q['class']) ? $q['class'] : ''?>">
	<?php
		if (isset($q['caption'])) {		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
	}
	?>
	<div class="data">
		<input name="<?=$q['name']?>" value="<?=isset($q['value']) ? $q['value'] : ''?>" <?=isset($q['attr']) ? $q['attr'] : ''?> />
	</div>
	<div class="clear"></div>
</div>
