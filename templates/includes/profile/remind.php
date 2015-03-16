<?php
//если письмо отправилось
if (isset($q['success'])) {
	echo html_array('form/message',i18n('profile|successful_remind',true));
} else {
?>
<?=$config['scripts']['jquery_validate']?>
<?=$q['text']?>
<?=isset($q['message']) ? html_array('form/message',$q['message']) : ''?>
<form method="post" class="form validate">
<?php
	echo html_array('form/input',array(
		'name'		=>	'email',
		'value'		=>	isset($q['email']) ? $q['email'] : '',
		'attr'		=>	'class="required email"',
	));
	echo html_array('form/captcha2');//скрытая капча
	echo html_array('form/button',array(
		'name'=>i18n('profile|remind_button'),
	));
?>
</form>
<?php } ?>
