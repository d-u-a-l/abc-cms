<?php
//если авторизирован
if (access('user auth',$user)) {
echo html_array('form/message',i18n('profile|successful_registration',true));
echo '<a href="/'.$modules['profile'].'/" title="'.i18n('profile|go_to_profile').'">'.i18n('profile|go_to_profile').'</a>';
} else {
?>
<?=$page['text']?>
<?=$config['scripts']['jquery_validate']?>
<?=isset($q['message']) ? html_array('form/message',$q['message']) : ''?>
<form method="post" class="form validate" enctype="multipart/form-value">
<?php
	echo html_array('form/input',array(
		'name'	=>	'email',
		'caption'	=>	i18n('profile|email',true),
		'value'	=>	isset($q['email']) ? $q['email'] : '',
		'attr'	=>	'class="required email"',
	));
	echo html_array('form/input',array(
		'name'	=>	'password',
		'caption'	=>	i18n('profile|password',true),
		'value'	=>	isset($q['password']) ? $q['password'] : '',
		'attr'	=>	'class="required" id="password" type="password" autocomplete="off" minlength="6"',
	));
	echo html_array('form/input',array(
		'name'	=>	'password2',
		'caption'	=>	i18n('profile|password2',true),
		'value'	=>	isset($q['password2']) ? $q['password2'] : '',
		'attr'	=>	'class="required confirm_password" type="password" autocomplete="off"',
	));
	echo html_array('profile/fields',isset($q['fields']) ? $q['fields'] : array());
	echo html_array('form/captcha2');//скрытая капча
	echo html_array('form/button',array(
		'name'	=>	i18n('profile|registration'),
	));
	?>
</form>
<?php } ?>