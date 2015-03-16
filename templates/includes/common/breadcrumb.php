<?php
if (isset($q['page']) && isset($q['module']) && is_array(@$q['page']) && is_array(@$q['module'])) $breadcrumb = array_merge($q['module'],$q['page']);
elseif (isset($q['page']) && is_array($q['page']))  $breadcrumb = $q['page'];
elseif (isset($q['module']) && is_array($q['module'])) $breadcrumb = $q['module'];
array_push($breadcrumb, array(i18n('common|breadcrumb_index'),'/'));
$count = count($breadcrumb)-1;
$content = '';
for ($i = $count; $i>0; $i--) {
	$content.= '<a href="'.$breadcrumb[$i][1].'">'.$breadcrumb[$i][0].'</a>'.i18n('common|breadcrumb_separator');//ссылка
}
$content.= $breadcrumb[0][0];
//echo '<h1>'.$breadcrumb[$i][0].'</h1>';
echo '<div class="breadcrumb">'.$content.'</div>';

?>