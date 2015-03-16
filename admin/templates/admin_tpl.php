<?php
$style = (isset($_COOKIE['a_style']) AND in_array($_COOKIE['a_style'],array('a','b','c','g'))) ? $_COOKIE['a_style'] : 'g';
$size = (isset($_COOKIE['a_size']) AND in_array($_COOKIE['a_size'],array('b','m','s'))) ? $_COOKIE['a_size'] : 'm';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Панель управления сайтом</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?=$config['scripts']['jquery']?>
<?=$config['scripts']['jquery_form']?>
<?=$config['scripts']['jquery_uploader']?>
<?=$config['scripts']['jquery_ui']?>
<?=$config['scripts']['tinymce']?>
<?=$config['scripts']['highslide']?>
<link  href="/admin/templates/reset.css" rel="stylesheet" type="text/css" />
<link href="/admin/templates/style.css?<?=filemtime(ROOT_DIR.'admin/templates/style.css')?>" rel="stylesheet" type="text/css" />
<script src="/admin/templates/dnd.js" type="text/javascript"></script>
<script src="/admin/templates/script.js?<?=filemtime(ROOT_DIR.'admin/templates/script.js')?>" type="text/javascript"></script>
</head>
<body class="<?=$style?>-style <?=$size?>-size <?=$pattern?>">
<?php
$menu = head($modules_admin,$get['m']);
?>
<table id="body" cellspacing="0" cellpadding="0">
	<tr>
		<td class="col"><div class="header"></div><?=$menu ? '<div class="menu_parent gradient"></div>' : ''?></td>
		<td class="main_col" nowrap="nowrap">

			<div class="header">
				<div class="abc"><a href="#" class="a">a</a><a href="#" class="b">b</a><a href="#" class="c">c</a></div>
				<div class="cms">Content<br />Management<br />System</div>
				<a class="sprite settings2" href="#"></a>
				<div class="login"><?=$user['email']?> &nbsp; <a href="?m=login&u=exit">[<?=a18n('profile_exit')?>]</a></div>
				<div class="settings">
					<b><?=a18n('template_size')?></b>
					<div class="size">
						<a href="#" class="b"><?=a18n('template_big')?></a><br />
						<a href="#" class="m"><?=a18n('template_medium')?></a><br />
						<a href="#" class="s"><?=a18n('template_small')?></a>
					</div>
					<b><?=a18n('template_color')?></b>
					<div class="color">
						<a href="#" class="a"></a>
						<a href="#" class="b"></a>
						<a href="#" class="c"></a>
						<a href="#" class="g"></a>
					</div>
				</div>
			</div>

			<?=$menu?>

			<div id="wrapper">
				<?=$content?>
				<?php
				if (is_array($filter)) {					?>
				<div id="filter">
					<?php
					foreach ($filter as $k=>$v) {
						echo is_array($v) ? call_user_func_array('filter', $v) : $v;

					}
					?>
					<div class="clear"></div></div>
					<?php
				}
				if (isset($table) AND is_array($table)) echo table($table,$query);
				?>

<?php
if (!in_array($get['m'],array('backup','restore'))) {	?>
			</div>

			<div id="footer">
				<div><?=date('Y')?> &copy; abc-cms.com</div>
				<a href="/" target="_blank" title="<?=a18n('go_to_site')?>"><?=a18n('go_to_site')?></a>
			</div>
		</td>
		<td class="col"><div class="header"></div><?=$menu ? '<div class="menu_parent gradient"></div>' : ''?></td>
	</tr>
</table>
<div id="dialog">
	<div class="dialog_data">
		<div class="dialog_text">Подтвердите удаление!</div>
		<a class="button green" href="#"><span>Отменить</span></a>
		<a class="button red" href="#"><span>Удалить</span></a>
	</div>
</div>
<div id="overlay"<?=($get['id'] AND isset($form) AND $overlay==true) ? ' class="display"' : ''?>></div>
	<?php
	if ($get['id'] AND isset($form)) require_once(ROOT_DIR.'admin/templates/admin_form.php');
	if (isset($table) && is_array($table)) require_once(ROOT_DIR.'admin/templates/contextmenu.php');
	?>
</body>
</html>
	<?php
}
?>