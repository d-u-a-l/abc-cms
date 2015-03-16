<?php
if (access('user auth')) {
	echo html_array('form/message',i18n('profile|successful_auth',true));
	echo '<a href="/'.$modules['profile'].'/" title="'.i18n('profile|go_to_profile').'">'.i18n('profile|go_to_profile').'</a>';

} else {
	echo $config['scripts']['jquery_validate'];
	echo isset($q['message']) ? html_array('form/message',$q['message']) : '';
?>
<form method="post" class="form validate" action="/<?=$modules['login']?>/enter/" >
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|email',true),
	'name'	=>	'email',
	'data'	=>	isset($q['email']) ? $q['email'] : '',
	'attr'	=>	'class="required email"',
));
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|password',true),
	'name'	=>	'password',
	'attr'	=>	'class="required" type="password" autocomplete="off"',
));
echo html_array('form/checkbox',array(
	'units'	=>	i18n('profile|remember_me',true),
	'name'	=>	'remember_me',
));
echo html_array('form/captcha2');//скрытая капча
echo html_array('form/button',array(
	'name'	=>	i18n('profile|enter'),
));
?>
<?php if (isset($modules['remind'])) {?>
	&nbsp; <a href="/<?=$modules['remind']?>/"><?=i18n('profile|remind')?></a>
<?php } ?>
</form>
<?php } ?>
