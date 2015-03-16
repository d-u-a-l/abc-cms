<?php
$title = htmlspecialchars($q['name']);
if ($i==1) {
	$old=0;
	echo '<div id="menu"><ul class="l1">';
}
if ($old>0 && $old>=$q['level'] ) echo '</li>';
if ($old>$q['level']) for ($n=$q['level']; $n<$old; $n++) echo '</ul></li>';
if ($old<$q['level'] && $old>0) echo '<ul class="l'.$q['level'].'">';
$url = $q['module']=='index' ? '' : ''.$q['url'].'/';
$class = @$u[1]==$q['url'] ? ' active' : '';

echo '<li class="l'.$q['level'].$class.'"><span></span><a class="l'.$q['level'].$class.'" href="/'.$url.'" title="'.$title.'">'.$q['name'].'</a>';

$old = $q['level'];

if ($i==$num_rows) {
	for ($a=1; $a<=$q['level']; $a++) echo '</li></ul>';
?>
</div>
<script type="text/javascript">
$(document).ready(function(){	$('#menu ul.l2').parent('li').addClass('parent');
	$('#menu li').hover(
		function () {
			$(this).addClass('hover').children('ul').fadeIn(300);
		},
		function () {
			$(this).removeClass('hover').children('ul').fadeOut(300);
		}
	);
	//$('#menu .l2.active').parents('li.l1').addClass('active');
});
</script>
<?php } ?>