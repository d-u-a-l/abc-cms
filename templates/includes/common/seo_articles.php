<?php
if($_SERVER['REQUEST_URI']) {	$request_url = explode('?',urldecode($_SERVER['REQUEST_URI']));
	//запрос на страницу с ссылками
	$query = "SELECT * FROM seo_pages WHERE display=1 AND url='".mysql_real_escape_string($request_url[0])."' LIMIT 1";
	if ($seo_page = mysql_select($query,'row')) {		if ($seo_page['articles'] AND $articles = mysql_select("
			SELECT * FROM news WHERE id IN (".$seo_page['articles'].")
		",'rows_id')) {
			?>
<style>
.articles_links {margin:0; padding:0 0 10px; list-style:none; background:#fff; box-radius:3px;}
.articles_links li {display:block; clear:both; padding:10px 10px 0}
.articles_links li a:first-child {float:left; margin:0 10px 0 0}
.articles_links li a:first-child img {width:100px;}
</style>
<ul class="products_links">
			<?php
			foreach ($articles as $k=>$v) {
				$title = htmlspecialchars($v['name']);
				?>
	<li>
		<a href="/<?=$modules['news']?>/<?=$v['id'].'-'.$v['url']?>/" title="<?=$title?>"><?=$v['name']?></a>
	</li>
				<?php
			}
			?>
	<div class="clear"></div>
</ul>
			<?php
		}
	}
}
?>