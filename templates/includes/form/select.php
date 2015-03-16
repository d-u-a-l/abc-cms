<div class="select <?=isset($q['class']) ? $q['class'] : ''?>">
	<?php
	if (isset($q['caption'])) {		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
	}
	?>
	<div class="data">
		<span>
			<select name="<?=$q['name']?>" <?=isset($q['attr']) ? $q['attr'] : ''?>><?=$q['select']?></select>
		</span>
	</div>
	<div class="clear"></div>
</div>