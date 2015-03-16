<?php
/*
$params = array(
	'Consumer key' => 'tmGWeMzYFE5RekGShPClDoObY',
	'client_secret' => 'kQMrM8vvgyhKwrdm0cGxnEUf12Xafv5L7oKKsEJcCprnKVcH5k',
);

$result = file_get_contents('https://api.twitter.com/1.1/' . '?' . urldecode(http_build_query($params)));
echo $result;

die();
*/

/*
$url = 'https://api.twitter.com/1.1/?';
$url.= 'oauth_consumer_key=tmGWeMzYFE5RekGShPClDoObY';
$url.= '&oauth_nonce=27f6ee10da7a5e03b947aec6ad8f76b0%26oauth_signature_method=HMAC-SHA1%26oauth_timestamp=1416134706%26oauth_token=2802666316-AopLObIB6xeDVDFsHEB3X4CVCRXbBmpTIZTrFsc%26oauth_version=1.0%26screen_name=dmitriy_smal%26statuses%252Fuser_timeline_json%253Fcount=2

*/
/*
echo htmlspecialchars_decode('
https%3A%2F%2Fapi.twitter.com%2F1.1%2F&oauth_consumer_key%3DtmGWeMzYFE5RekGShPClDoObY%26oauth_nonce%3D27f6ee10da7a5e03b947aec6ad8f76b0%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1416134706%26oauth_token%3D2802666316-AopLObIB6xeDVDFsHEB3X4CVCRXbBmpTIZTrFsc%26oauth_version%3D1.0%26screen_name%3Ddmitriy_smal%26statuses%252Fuser_timeline_json%253Fcount%3D2
');
*/
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=2&screen_name=dmitriy_smal';
//$url = 'https://api.twitter.com//1/statuses/update.json?include_entities=true';

$opts = array(
	'http'=>array(
		//'method'=>"GET",
		'header'=> array(
'Authorization:
OAuth oauth_consumer_key="DC0sePOBbQ8bYdC8r4Smg",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1416138928",oauth_nonce="755403107",oauth_version="1.0",oauth_token="2802666316-2dw5xzaL2mjLx8kPusI78eEm43dMeoEhq227W0s",oauth_signature="8Z%2BinQDk3tpW8aqn00hEWPBtnzE%3D"
'
		),
	)
);
$context = stream_context_create($opts);

$result	= file_get_contents($url);//,false,$context);
echo $result;


?>