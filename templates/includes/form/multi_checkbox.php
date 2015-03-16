<div class="multi_checkbox <?=@$q['class']?>">
	<?php
	if (isset($q['caption'])) {
		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
	}
	?>
	<div class="data">
		<?php
		$data = is_array($q['data']) ? $q['data'] : mysql_select($q['data'],'array');
		$value = @$q['value']!='' ? explode(',',$q['value']) : array();
		foreach ($data as $k=>$v) {
			$checked = in_array($k,$value) ? ' checked="checked" ' : '';
			?>
		<label><input name="" type="checkbox" value="<?=$k?>"<?=$checked?>/><span><?=$v?></span></label>
			<?php
		}
		?>
	</div>
	<input name="<?=$q['name']?>" type="hidden" value="<?=$q['value']?>">
	<div class="clear"></div>
</div>