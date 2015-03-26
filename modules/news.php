<?php

$news = false;

//одна запись
if ($u[2]) {
	$id = intval(explode2('-',$u[2]));
	if ($u[3]=='' AND $news = mysql_select("
		SELECT *
		FROM news
		WHERE id = '".$id."' AND display = 1
	",'row')) {
		$page = array_merge($page,$news);
		//переадреация на корректный урл
		if (explode2('-',$u[2],2)!=$page['url']) die(header('location: /'.$modules['news'].'/'.$page['id'].'-'.$page['url'].'/'));
		$html['content'] = html_array('news/text',$page);
		$breadcrumb['module'][] = array($page['name'],'/'.$modules['news'].'/'.$page['id'].'-'.$page['url'].'/');
	}
	else $error++;
}
//список записей
else {
	$html['content'] = html_query('news/list normal',"
		SELECT id,date,name,url,text
		FROM news
		WHERE display = 1
		ORDER BY date DESC
	",'',60*60);
}

?>