<?php

$user_types = mysql_select("SELECT id,ut_name name FROM user_types",'array');

//исключение при редактировании модуля
if ($get['u']=='edit') {
	if ($post['change']==1) {
		$post['email'] = strtolower($post['email']);
		$post['hash'] = md5($post['email'].md5($post['password']));
	}
	else unset($post['email']);
	unset($post['password'],$post['change']);
	$post['fields'] = isset($post['fields']) ? serialize($post['fields']) : '';
}
if ($get['u']=='' OR $get['u']=='form') {
	$modules['profile'] = mysql_select("SELECT url FROM pages WHERE module='profile' LIMIT 1",'string');
}

$a18n['date']	= 'регистрация';
$a18n['type']	= 'статус';
$a18n['last_visit']	= 'последний визит';

$table = array(
	'id'		=>	'date last_visit id email',
	'email'		=>	'<a target="_blank" href="/'.$modules['profile'].'/?email={email}&hash={hash}">{email}</a>',
	'type'		=>	$user_types,
	'last_visit'	=> 'date',
	'date'		=>	'date',
);

$where = (isset($get['type']) && $get['type']>0) ? "AND users.type = '".$get['type']."' " : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(users.email) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(users.fields) like '%".mysql_real_escape_string(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT users.*
	FROM users
	WHERE 1 ".$where;

$filter[] = array('type',$user_types,'-статус-');
$filter[] = array('search');

$delete =  array();

$form[] = array('input td4','email',true,array('attr'=>'disabled="disabled"'));
$form[] = array('input td4','password','',array('attr'=>'disabled="disabled"'));
$form[] = array('checkbox td4','change','',array('name'=>'сменить email и пароль','attr'=>'onchange=$(this).closest(\'form\').find(\'input[name=email],input[name=password]\').prop(\'disabled\',!this.checked)'));
$form[] = array('select td4','type',array(true,$user_types));
$form[] = array('input td4','date',true,array('name'=>'дата регистрации'));
$form[] = array('input td4','last_visit',true);

$form[] = '<div class="clear"></div>';
if ($get['u']=='form' OR $get['id']>0) {
	$form[] = '<h2>Дополнительные параметры</h2>';
	$fields = unserialize($post['fields']);
	$result = mysql_query("
		SELECT *
		FROM user_fields
		WHERE display = 1
		ORDER BY rank DESC
	");	echo mysql_error();

	$parameter = array();
	while ($q = mysql_fetch_assoc($result)) {
		$values = $q['values'] ? unserialize($q['values']) : '';
		if(!isset($fields[$q['id']][0])) $fields[$q['id']][0] = '';
		switch ($q['type']) {
			case 1:	$form[] = array('input td3','fields['.$q['id'].'][]',$fields[$q['id']][0],array('name'=>$q['name']));					break;
			case 2:	$form[] = array('select td3','fields['.$q['id'].'][]',array($fields[$q['id']][0],$values),array('name'=>$q['name']));	break;
			case 3:	$form[] = array('textarea td12','fields['.$q['id'].'][]',$fields[$q['id']][0],array('name'=>$q['name']));
		}

	}
}