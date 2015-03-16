<div class="file <?=isset($q['class']) ? $q['class'] : ''?>">
	<?php
		if (isset($q['caption'])) {
		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
		}
	?>
	<div class="data">
		<input multiple="multiple" name="<?=$q['name']?>" type="file" />
	</div>
	<div class="clear"></div>
</div>
