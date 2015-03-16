<div class="news_text">
	<div class="date"><?=date2($q['date'],'%d.%m.%y')?></div>
	<div<?=editable('news|text|'.$q['id'])?>><?=$q['text']?></div>
</div>
