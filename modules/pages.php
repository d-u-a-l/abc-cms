<?php

//список внутренних страниц
$query = "SELECT * FROM pages WHERE parent = '".$page['id']."' AND display = 1 ORDER BY left_key"; //echo $query;
$html['page_children'] = html_query('pages/children',$query,'',60*60);

//список страниц этого же уровня
$query = "SELECT * FROM pages WHERE parent = '".$page['parent']."' AND display = 1 ORDER BY left_key"; //echo $query;
$html['page_list'] = html_query('pages/list',$query,'',60*60);

?>
