<?php

//варианты шаблона письма /templates/includes/letter_templates/
$template = array(
	1 => 'основной шаблон'
);

$tabs = mysql_select("SELECT id,name FROM languages ORDER BY rank DESC",'array');

if ($get['u']=='edit') {
	if ($get['id']>0) {} else {
		$get['id'] = mysql_fn('insert',$get['m'],array('template'=>$post['template']));
	}
	$path = ROOT_DIR.'files/letter_templates/'.$get['id'].'/';
	if (is_dir($path) || mkdir($path,0755,true)) {
		foreach ($tabs as $k=>$v) {
			if (is_dir($path.$k) || mkdir($path.$k,0755,true)) {
				$fp = fopen($path.$k.'/subject.php','w');
				fwrite($fp,@$post['subject'.$k]);
				fclose($fp);
				$fp = fopen($path.$k.'/text.php','w');
				fwrite($fp,@$post['text'.$k]);
				fclose($fp);
			}
		}
	}
	foreach ($tabs as $k=>$v) {
		unset($post['subject'.$k],$post['text'.$k]);
	}
}

$a18n['sender'] = 'отправитель';
$a18n['receiver'] = 'получатель';

$table = array(
	'id'		=>	'name id',
	'template'	=>	$template,
	'name'		=>	'',
	'sender'	=>	'',
	'receiver'	=>	'',
	'description'	=>	''
);

$query = "
	SELECT *, IF (sender='','".$config['sender']."',sender) sender, IF (receiver='','".$config['receiver']."',receiver) receiver
	FROM letter_templates
	WHERE 1
";


if ($get['u']=='form' AND $get['id']>0) {
	foreach ($tabs as $k=>$v) {
		$path = ROOT_DIR.'files/letter_templates/'.$get['id'].'/';
		$post['subject'.$k] = '';
		if (is_file($path.$k.'/subject.php')) {
			$handle = @fopen($path.$k.'/subject.php', "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) $post['subject'.$k].= $buffer;
				fclose($handle);
			}
		}
		$post['text'.$k] = '';
		if (is_file($path.$k.'/text.php')) {
			$handle = @fopen($path.$k.'/text.php', "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) $post['text'.$k].= $buffer;
				fclose($handle);
			}
		}
	}

}




$form[1][] = array('input td3','name',true);
$form[1][] = array('select td3','template',array(true,$template,''));
$form[1][] = array('input td3','sender',true,array('help'=>'emeil отправителя письмо с сервера','attr'=>'placeholder="'.$config['sender'].'"'));
$form[1][] = array('input td3','receiver',true,array('help'=>'emeil получателя письма, если он не задан в модуле','attr'=>'placeholder="'.$config['receiver'].'"'));
$form[1][] = array('input td12','description',true);

foreach ($tabs as $k=>$v) {
	$form[$k][] = array('input td12','subject'.$k,true,array('name'=>'тема письма'));
	$form[1][] = '<br />Настройка текста в шапке и подвале письем в <a target="_blank" href="?m=languages#6">словаре</a>';
	$form[$k][] = array('textarea td12','text'.$k,true,array('name'=>'текст письма','attr'=>'style="height:400px"'));
}

$content = '<br />Здесь можно указать индивидуально отправителя и получателя письма, глобальная настройка <a href="/admin.php?m=config">тут</a>';

?>