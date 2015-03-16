<?=$config['scripts']['jquery_validate']?>
<?=isset($q['message']) ? html_array('form/message',$q['message']) : ''?>
<form method="post" class="form validate" enctype="multipart/form-data">
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|new_password',true),
	'name'	=>	'password',
	'value'	=>	'0000000000',
	'attr'	=>	'class="required" type="password" minlength="6"',
));
echo html_array('profile/fields',isset($q['fields']) ? $q['fields'] : array());
echo html_array('form/button',array(
'name'	=>	i18n('profile|save')
));
?>
</form>
