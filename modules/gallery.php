<?php

$galleries = mysql_select("
		SELECT *
		FROM gallery
		WHERE display = 1 AND img!=''
		ORDER BY rank DESC,name
	",'rows');
//одна запись
if ($u[2] OR count($galleries)==1) {	if ($u[2]) {
		$id = intval(explode2('-',$u[2]));  //echo $id;
		$query = "
			SELECT *
			FROM gallery
			WHERE id = '".$id."' AND display = 1
			LIMIT 1
		";
		$page = mysql_select($query,'row');  // print_r($page);		if ($page AND $u[2]!=$page['id'].'-'.$page['url']) //переадреация на корректный урл
			die(header('location: /'.$modules['gallery'].'/'.$page['id'].'-'.$page['url'].'/'));	} else $page = $galleries[0];
	if ($page) {		$html['content'] = html_array('gallery/text',$page);
		$breadcrumb['module'][] = array($page['name'],'/'.$modules['gallery'].'/'.$page['id'].'-'.$page['url'].'/');
	}
	else $error++;

//список записей
} else {	$html['content'] = html_query('gallery/list',$galleries,'');
}



?>