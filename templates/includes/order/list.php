<?php if ($i==1) { ?>
<table class="order_list">
<tr>
	<th class="id">#</th>
	<th class="status"><?=i18n('basket|order_status',true)?></th>
	<th class="paid"></th>
	<th class="total"><?=i18n('basket|total',true)?></th>
</tr>
<?php } ?>
<tr>
	<td class="id"><a href="/<?=$modules['profile']?>/orders/<?=$q['id']?>/"><?=i18n('basket|order_name')?> № <?=$q['id']?> <?=i18n('basket|order_from')?> <?=date2($q['date'],'%d.%m.%Y')?></a></td>
	<td class="status"><?=$q['ot_name']?></td>
	<td class="paid"><?=$q['paid']==1?i18n('order|paid',true):i18n('order|not_paid',true)?></td>
	<td class="total"><?=$q['total']?> <?=i18n('shop|currency')?></td>
</tr>
<?php if ($num_rows==$i) {?>
</table>
<?php } ?>
