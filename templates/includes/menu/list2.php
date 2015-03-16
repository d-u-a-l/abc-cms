<?php
if ($i==1) echo '<ul id="menu2">';
$title = htmlspecialchars($q['name']);
$url = $q['module']=='index' ? '' : ''.$q['url'].'/';
$class = @$u[1]==$q['url'] ? ' class="active"' : '';
echo '<li'.$class.'><span></span><a  href="/'.$url.'" title="'.$title.'">'.$q['name'].'</a></li>';
if ($i==$num_rows) echo '</ul>';
?>