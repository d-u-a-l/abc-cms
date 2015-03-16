<?php
//если письмо отправилось
if (isset($q['success'])) {
	echo html_array('form/message',i18n('feedback|message_is_sent',true));
} else {
?>
<?=$config['scripts']['jquery_validate']?>
<noscript><?=i18n('validate|not_valid_captcha2')?></noscript>
<?=isset($q['message']) ? html_array('form/message',$q['message']) : ''?>
<form method="post" class="form validate" enctype="multipart/form-data">
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('feedback|email',true),
	'name'		=>	'email',
	'value'		=>	isset($q['email']) ? $q['email'] : '',
	'attr'		=>	'class="required email"',
));
echo html_array('form/input',array(
	'caption'	=>	i18n('feedback|name',true),
	'name'		=>	'name',
	'value'		=>	isset($q['name']) ? $q['name'] : '',
	'attr'		=>	'class="required"',
));
echo html_array('form/textarea',array(
	'name'		=>	'text',
	'caption'	=>	i18n('feedback|text',true),
	'value'		=>	isset($q['text']) ? $q['text'] : '',
	'attr'		=>	'class="required"',
));
echo html_array('form/file',array(
	'caption'	=>	i18n('feedback|attach',true),
	'name'		=>	'attaches[]',
));
echo html_array('form/captcha2');//скрытая капча
echo html_array('form/button',array(
	'name'	=>	i18n('feedback|send'),
));
?>
</form>
<?php } ?>