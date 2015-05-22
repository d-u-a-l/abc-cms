<?php

if ($get['u']=='edit') {
	if ($post['mailer']>0) {
		$subscribers = mysql_select("SELECT * FROM subscribers WHERE display=1 ORDER BY id",'rows');
		$now = date('Y-m-d H:i:s');
		require_once(ROOT_DIR.'functions/index_func.php');
		$modules['subscribe'] = mysql_select("SELECT url FROM pages WHERE module='subscribe'",'string');
		if (is_array($subscribers)) foreach ($subscribers as $k=>$v){
			$letter = array(
				'date'			=> $now,
				'subject'		=> $post['subject'],
				'sender'		=> $post['sender'],
				'sender_name'	=> $post['sender_name'],
				'receiver'		=> $v['email'],
				'text'			=> $post['text'],
			);
			$data = array_merge($letter,array('date'=>$v['date']));
			$letter['text'] = html_array('subscribe/letter',$data);
			if ($post['mailer']==1) $letter['date_sent'] = $now;
			mysql_fn('insert','letters',$letter);
		}
	}
	unset($post['mailer']);
}

$a18n['subject']		= 'тема рассылки';
$a18n['sender']			= 'email отправителя';
$a18n['sender_name']	= 'имя отправителя';

$table = array(
	'id'			=>	'id:desc date',
	'subject'		=>	'',
	'sender'		=>	'',
	'sender_name'	=>	'',
	'date'			=>	'date',
);

$form[] = array('input td8','subject',true);
$form[] = array('input td4','date',true);
$form[] = array('input td4','sender',true);
$form[] = array('input td4','sender_name',true);
$form[] = array('select td4','mailer',array('',array(0=>'не рассылать',1=>'тестирование',2=>'реальная рассылка')),
	array(
		'name'=>'рассылка',
		'help'=>'тестирование - письма будут сгенерированы но не отправлены
реальная рассылка - будут отправлены письма'
	)
);
$form[] = array('textarea td12','text',true,array('attr'=>'style="height:300px"',));