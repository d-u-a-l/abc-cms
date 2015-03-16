<div class="submit <?=isset($q['class']) ? $q['class'] : ''?>">
	<div class="data">
		<input name="<?=$q['name']?>" value="<?=isset($q['value']) ? $q['value'] : ''?>" type="submit" <?=isset($q['attr']) ? $q['attr'] : ''?>/>
	</div>
	<div class="clear"></div>
</div>
