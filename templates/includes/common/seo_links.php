<?php
$links = false;
$count = 0;
if($_SERVER['REQUEST_URI']) {
	//������ �� �������� � ��������
	$query = "SELECT * FROM seo_pages WHERE url='".mysql_real_escape_string($request_url[0])."' LIMIT 1";
	if ($seo_page = mysql_select($query,'row')) {
			}
		}
	}
	//���� ��� �������� �� �������
	else {
			'display'	=> 1,
			'exist'		=> 1,
			'name'		=> $page['name'],
			'url'		=> $request_url[0]
		);
		$seo_page['id'] = mysql_fn('insert','seo_pages',$seo_page);
	}

	//���� ������ ������ 5
	if ($seo_page['display']==1 AND $count<5) {
			SELECT sl.*
			FROM seo_links sl
			WHERE sl.url != '".mysql_real_escape_string($request_url[0])."'
				AND (SELECT COUNT(d.id) FROM `seo_links-pages` d WHERE d.parent=sl.id)<sl.limit
			GROUP BY sl.url
			ORDER BY RAND()
			LIMIT ".(5-$count)."
		",'rows_id')) {
			foreach ($links_new as $k=>$v) {
			}
			//���������� ������
			//��������� ������
			mysql_fn('update','seo_pages',array(
				'id'=>$seo_page['id'],
				'links'=>implode(',',array_keys($links)) //�� ������ ����� �������
			));
		}
	}
}
//������ ������
if ($links AND count($links)>0) {
<style>
.products_links {margin:0; padding:0 0 10px; list-style:none; background:#fff; box-radius:3px;}
.products_links li {display:block; clear:both; padding:10px 10px 0}
.products_links li a:first-child {float:left; margin:0 10px 0 0}
.products_links li a:first-child img {width:100px;}
</style>
<ul class="products_links">
	<?php
	foreach ($links as $k=>$v) {
		$title = htmlspecialchars($v['name']);
		$v['img'] = $v['img'] ? $v['img'] : '/templates/images/no_img.png';
		?>
	<li>
		<a href="<?=$v['url']?>" title="<?=$title?>"><img src="<?=$v['img']?>" alt="<?=$title?>"></a>
		<a href="<?=$v['url']?>" title="<?=$title?>"><?=$v['name']?></a>
	</li>
			<?php
	}
	?>
	<div class="clear"></div>
</ul>
	<?php
}
?>