<?php
$h1 = 'style="font:bold 16px/18px Arial; padding:10px 0; margin:0"';
$h2 = 'style="font:bold 14px/16px Arial; padding:10px 0 5px; margin:0"';
$table = 'style="font:14px/16px Arial; border-collapse:collapse; border-spacing:0;"';
$th = 'style="text-align:left; padding:0 0 0 5px; font-weight:normal; border-bottom:1px solid #999"';
$td = 'style="padding:3px;"';
$td_right = 'style="padding:3px; text-align:right"';
$basket = unserialize($q['basket']);
$page['name'] = i18n('basket|order_name').' № '.$q['id'].' '.i18n('basket|order_from').' '.date2($q['date'],'%d.%m.%Y');
?>
<h1 <?=$h1?>><?=$page['name']?></h1>

<?=i18n('basket|order_status')?>: <?=$q['ot_name']?>

<div style="padding:10px 0"><?=$q['ot_text']?></div>

<table <?=$table?>>
<thead>
	<tr>
		<th <?=$th?>><?=i18n('basket|product_id')?></th>
		<th <?=$th?>><?=i18n('basket|product_name')?></th>
		<th <?=$th?>><?=i18n('basket|product_price')?></th>
		<th <?=$th?>><?=i18n('basket|product_count')?></th>
		<th <?=$th?>><?=i18n('basket|product_cost')?></th>
	</tr>
</thead>
<tbody>
<?php
$i = 0;
foreach ($basket['products'] as $k=>$v) {	$i=$i==0 ? 1 : 0;	$sum = $v['price']*$v['count'];
?>
<tr>
	<td <?=$td_right?>><?=$v['id']?></td>
	<td <?=$td?>><?=$v['name']?></td>
	<td <?=$td_right?>><?=number_format($v['price'],2,'.','')?> <?=i18n('shop|currency')?></td>
	<td <?=$td_right?>><?=$v['count']?></td>
	<td <?=$td_right?>><?=number_format($sum,2,'.','')?> <?=i18n('shop|currency')?></td>
</tr>
<?php } if ($basket['delivery']['type']) { ?>
<tr>
	<td colspan="4" <?=$td_right?>><?=i18n('basket|delivery_cost')?>
	<?php
	$delivery = mysql_select("SELECT * FROM order_deliveries WHERE id = '".intval($basket['delivery']['type'])."'",'row');
	if ($delivery) {
		echo '('.$delivery['name'].')';
	}
	?>:</td>
	<td <?=$td_right?>><?=number_format($basket['delivery']['cost'],2,'.','')?> <?=i18n('shop|currency')?></td>
</tr>
<?php } ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="4" <?=$td_right?>><?=i18n('basket|total')?>:</td>
		<td <?=$td_right?>><b><?=number_format($q['total'],2,'.','')?> <?=i18n('shop|currency')?></b></td>
	</tr>
</tfoot>
</table>


<br /><h2 <?=$h2?>><?=i18n('basket|profile')?></h2>
<table <?=$table?>>
<tr>
	<td <?=$td?>><?=i18n('profile|email')?>: &nbsp; </td>
	<td <?=$td?>><b><?=$q['email']?></b></td>
</tr>
<?php if (is_array($basket['user'])) {
$result = mysql_query("SELECT * FROM user_fields WHERE display = 1 ORDER BY rank DESC");
while ($f = mysql_fetch_assoc($result)) if (isset($basket['user'][$f['id']])) {?>
<tr>
	<td <?=$td?>><?=$f['name']?>: &nbsp; </td>
	<td <?=$td?>><b><?=$basket['user'][$f['id']][0]?></b></td>
</tr>
<?php } } ?>
</table>


<?php if ($basket['text']) {?>
<br />
<h2 <?=$h2?>><?=i18n('basket|comment')?></h2>
<?=str_replace ("\n",'<br />',$basket['text']);?>
<?php } ?>
