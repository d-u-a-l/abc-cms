<?php
$q['text'] = strip_tags($q['text']);
$text = iconv_strlen($q['text'])>300 ? iconv_substr($q['text'],0,300,"UTF-8").'..' : $q['text'];
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
?>
<div class="news_list">
	<div class="date"><?=date2($q['date'],'%d.%m.%y')?></div>
	<div class="name">
		<a href="/<?=$modules['news']?>/<?=$q['id']?>-<?=$q['url']?>/" title="<?=$title?>"><?=$q['name']?></a>
	</div>
	<div class="text">
		<?=$text?>
	</div>
	<div class="next">
		<a href="/<?=$modules['news']?>/<?=$q['id']?>-<?=$q['url']?>/"><?=i18n('common|wrd_more')?></a>
	</div>
</div>