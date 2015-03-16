<?php if (isset($modules['basket'])) {
$count = isset($_SESSION['basket']['count']) ? $_SESSION['basket']['count'] : 0;
$total = isset($_SESSION['basket']['total']) ? $_SESSION['basket']['total'] : 0;
?>
<div id="basket_info">
	<a href="/<?=$modules['basket']?>/"></a>
	<div class="full" <?=$count>0 ? '' : ' style="display:none"' ?>>
		<a href="/<?=$modules['basket']?>/"><?=i18n('basket|product_count')?> <span class="count"><?=$count?></span>
		<br /><?=i18n('basket|product_summ')?> <span class="total"><?=$total?></span> <?=i18n('shop|currency')?></a>
	</div>
	<?php if ($count==0) {?>
	<div class="empty">
		<?=i18n('basket|empty',true)?>
	</div>
	<?php } ?>
</div>
<div id="basket_message" class="window window_message" data-attr="middle">
	<div class="window_data">
		<div class="window_close"></div>
		<?=i18n('basket|product_added',true)?>
		<br /><br />
		<a style="float:right" href="#" class="window_close" title="<?=i18n('basket|go_next')?>"><?=i18n('basket|go_next')?></a>
		<a href="/<?=$modules['basket']?>/" title="<?=i18n('basket|go_basket')?>"><?=i18n('basket|go_basket')?></a>
	</div>
</div>
<?php } ?>