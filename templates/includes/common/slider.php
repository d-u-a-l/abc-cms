<?php if ($i==1) { ?>
<div id="slider">
	<div class="slider">
<?php } ?>
	<div <?=$i==1 ? 'class="active" ' : ''?>data-i="<?=$i?>" <?=$q['img'] ? 'style="background-image:url(\'/files/slider/'.$q['id'].'/img/p-'.$q['img'].'\')"' : ''?>>
		<div>
			<?=$q['text']?>
			<a href="<?=$q['url']?>" title="<?=$q['name']?>"><?=i18n('common|wrd_more')?></a>
		</div>
	</div>
<?php if ($i==$num_rows) { ?>
	</div>
	<?php if ($num_rows>1) {?>
	<div class="switches">
		<?php for ($n=1; $n<=$num_rows; $n++) { ?>
		<a  <?=$n==1 ? 'class="active" ' : ''?>data-i="<?=$n?>" href="#"></a>
		<?php }?>
	</div>
	<?php } ?>
</div>
<?php if ($num_rows>1) {?>
<script type="text/javascript">
$(window).bind('load', function() {
	setInterval(function() {
		var i = $('.slider div.active').data('i');
		var next = i+1;
		slider(next);
	}, 5000);
	$('#slider .switches a').click(function(){
		var next = $(this).data('i');
		slider(next);
		return false;
	});
});
function slider(next) {
	if (next><?=$num_rows?>) next=1;
	if (next==0) next=<?=$num_rows?>;
	$('.slider div.active').fadeOut(1200).removeClass('active');
	$('.slider div[data-i="'+next+'"]').fadeIn(1200).addClass('active');
	$('.switches a.active').removeClass('active');
	$('.switches a[data-i="'+next+'"]').addClass('active');
}
</script>
<?php } ?>
<?php } ?>