<?php
/*if ($q['merchant']==1) {
}
//���������
else {*/
	$IncCurrLabel = array(
		1 => '',
		2 => '',
		3 => 'YandexMerchantR',
		4 => 'WMR',
		5 => 'Qiwi30OceanR',
		6 => 'TerminalsElecsnetOceanR',
		7 => 'BANKOCEAN2R',
	);
	$inv_desc = $page['id'].' | '.$page['date'];
	$action = 'https://auth.robokassa.ru/Merchant/Index.aspx?';
	$action.= 'MrchLogin='.$config['robokassa_login'];
	$action.= '&OutSum='.$page['total'];
	$action.= '&InvId='.$page['id'];
	$action.= '&IncCurrLabel='.$IncCurrLabel[$q['merchant']];
	$action.= '&Desc='.$inv_desc;
	$action.= '&SignatureValue='.$crc;
	$action.= '&Shp_item=1';
	$action.= '&Culture=ru';
	$action.= '&Encoding=utf-8';
//}

if ($i==1) {
<br /><br />
<h2><?=i18n('order|payments',true)?></h2>
<div class="order_payments">
<form action="<?=$action?>" method="post">
<?php
}
?>
<label>
	<input name="payment" type="radio" value="<?=$action?>" <?=$i==1?'checked="checked"':''?>/>
	<span><?=$q['name']?></span>
</label>
<?php
if ($i==$num_rows) {
	?>

	<input type="submit" value="<?=i18n('order|pay')?>" />
</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
		$('.order_payments form').attr('action',action);
	});
});
</script>
<?php
}
?>