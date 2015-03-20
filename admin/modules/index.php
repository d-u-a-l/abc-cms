<?php

$modules_imgs = array(
	'news'		=> 'news',
	'pages'		=> 'sitemap',
	'shop'		=> 'shop',
	'settings'	=> 'settings',
	'dictionary'=> 'dictionary',
	'backup'	=> 'archive',
	'design'	=> 'design',
	'users'		=> 'users',
	'catalog'	=> 'catalog',
	'gallery'	=> 'gallery',
	'subscribe'	=> 'subscribe',
	'seo'		=> 'seo',
	'synchronization' => 'synchro',
	'feedback'	=> 'feedback',
	'config'	=> 'settings',
	//'logs'			=> 'logs',
);

$content.= '<ul class="modules">';
foreach ($modules_admin as $key => $value) {
		foreach ($value as $k=>$v) {
			if (access('admin module',$v)==false) unset($modules_admin[$key][$k]);
		}
	}
	elseif (access('admin module',$value)==false) unset($modules_admin[$key]);
}
foreach ($modules_admin as $key => $value) {
	if (is_array($value)) {
		$i = 0;
		foreach ($value as $k=>$v) {
			if (count($value) > 1) {
				$i++;
				if ($i==1) {
					$content.= '<li><a href="/admin.php?m='.$v.'"><b>'.a18n($key).'</b></a>';
					if (isset($modules_imgs[$key])) $content.= '<div><span class="'.$modules_imgs[$key].'"></span></div>';
					$content.= '<ul>';
				}
				$content.= '<li><a href="/admin.php?m='.$v.'">&bull; '.a18n($k).'</a></li>';
			}
			else {
				$content.= '</li>';
			}
		}
		if ($i) $content.= '</ul></li>';
	}
	elseif (access('admin module',$value)) {
		$content.= '</li>';
	}
}
$content.= '<div class="clear"></div>';
$content.= '</ul>';
$content.= '
<script type="text/javascript">
$(document).ready(function(){
	$(".modules>li").hover(
		function(){
			if ($("ul",this).length>0) {
				$("ul",this).animate({marginTop:-165}, 300);
				$("ul li",this).animate({paddingBottom:0}, 450);
				$("div",this).animate({marginLeft:170}, 700);
			}
		},
		function(){
			$("ul",this).animate({marginTop:0}, 300);
			$("ul li",this).animate({paddingBottom:150}, 450);
			$("div",this).animate({marginLeft:0}, 500);
		}
	);
});
</script>';


?>