<?php

$query = "
	SELECT name,level,url,module
	FROM pages
	WHERE display = 1
	ORDER BY left_key
";

$html['content'] = html_query('page/sitemap',$query);

?>