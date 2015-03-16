<div class="input2 <?=isset($q['class']) ? $q['class'] : ''?>">
	<?php
	if (isset($q['caption'])) {		?>
	<div class="caption"><?=$q['caption']?></div>
		<?php
	}
	$data = explode('-',@$q['value'])
	?>
	<div class="data">
		<input name="" value="<?=(isset($data[0]) AND $data[0]>0) ? $data[0] : ''?>" <?=isset($q['attr'][0]) ? $q['attr'][0] : ''?>/> &ndash;
		<input name="" value="<?=@$data[1]>0 ? $data[1] : ''?>" <?=isset($q['attr'][0]) ? $q['attr'][0] : ''?>/>
	</div>
	<input name="<?=@$q['name']?>" type="hidden" value="<?=@$q['value']?>">
	<div class="clear"></div>
</div>