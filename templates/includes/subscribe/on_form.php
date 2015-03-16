<?php
if (isset($q['success'])) {	?>
	<?=i18n('subscribe|on_success',true)?>
	<?php
}
else {	?>
<?=$config['scripts']['jquery_validate']?>
<form class="form validate" action="" method="post">
<?=html_array('form/input',array(
	'name'	=>	'email',
	'caption'	=>	i18n('profile|email',true),
	'value'	=>	isset($q['email']) ? $q['email'] : '',
	'attr'	=>	'class="required email"',
));?>
<?=html_array('form/button',array(
	'name'	=>	i18n('subscribe|on_button'),
));?>
<?=html_array('form/captcha2');?>
</form>
	<?php
}
?>