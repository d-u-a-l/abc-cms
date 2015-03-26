<?php
$page['title']			= isset($page['title']) ? filter_var($page['title'], FILTER_SANITIZE_STRING) : filter_var($page['name'],FILTER_SANITIZE_STRING);
$page['description']	= isset($page['description']) ? filter_var($page['description'], FILTER_SANITIZE_STRING) : $page['title'];
$page['keywords']		= isset($page['keywords']) ? filter_var($page['keywords'], FILTER_SANITIZE_STRING) : $page['title'];
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?=$page['title']?></title>
<meta name="description" content="<?=$page['description']?>" />
<meta name="keywords" content="<?=$page['keywords']?>" />
<?=html_sources('return','css_reset css_common jquery')?>
<?=i18n('common|txt_meta')?>
<?=html_sources('return','script_common')?>
<?=access('editable scripts') ? html_sources('return','tinymce editable') : ''?>
<?=html_sources('head')?>
</head>

<body>
<div id="body">
<div id="header">
    <a href="/" title="<?=i18n('common|site_name')?>" id="logo"><img src="/<?=$config['style']?>/images/logo.png" alt="<?=i18n('common|site_name')?>" /></a>
    <div id="header_text"><?=i18n('common|txt_head',true)?></div>
    <?=html_array('order/basket_info')?>
    <?=html_array('profile/login_form')?>
</div>

<?=html_query('menu/list',"
    SELECT name,url,module,level
    FROM pages
    WHERE display=1 AND level < 3 AND menu = 1
    ORDER BY left_key
",'',60*60,'json')?>

<div id="wrapper">
	<div id="main_col">
		<?php
		if ($html['module']=='index') include(ROOT_DIR.$config['style'].'/includes/common/index.php');
		else {
			if (isset($breadcrumb))
				echo html_array('common/breadcrumb',$breadcrumb);
			if (file_exists(ROOT_DIR.$config['style'].'/includes/'.$html['module'].'/template.php')) include(ROOT_DIR.$config['style'].'/includes/'.$html['module'].'/template.php');
			elseif($html['module']=='basket') echo $html['content'];
			else {
				?>
		<div class="content">
			<h1><?=$page['name']?></h1>
			<?=$html['content']?>
		</div>
				<?php
			}
		}?>
	</div>

	<div id="left_col">
		<?php if (isset($modules['shop'])) {?>
		<?=html_query('menu/category',"
			SELECT id,name,url,level
			FROM shop_categories
			WHERE display = 1
			ORDER BY left_key
		",'',60*60,'json'); ?>

		<?=html_query('shop/product_random',"
			SELECT sp.*,sc.url category_url
			FROM shop_products sp, shop_categories sc
			WHERE sp.display = 1 AND sp.img!='' AND sp.category=sc.id
			ORDER BY RAND()
			LIMIT 1
		",''); ?>
		<?php } ?>
		<?=$error==0?html_array('common/seo_links'):''?>
		<?=$error==0?html_array('common/seo_articles'):''?>
	</div>
	<div class="clear"></div>
</div>
<?=html_query('menu/list2',"
	SELECT name,url,module,level
	FROM pages
	WHERE display=1 AND level=1 AND menu2 = 1
	ORDER BY left_key
",'',60*60,'json')?>
</div>
<div id="footer">
	<div class="box"><?=i18n('common|txt_footer')?></div>
</div>
<?=html_sources('footer')?>
</body>
</html>