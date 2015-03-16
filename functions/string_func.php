<?php

//��������� �������� ����
function keywords($str) { //04.02.10 ����� �������� ���� � ������
	$keywords = '';
	if (strlen($str)>0) {
		$str = preg_replace("/&[\w]+;/", ' ',$str);	//������ �������� ���� &nbsp; �� ������
		$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
		$str = preg_replace('~[^-����-�a-z0-9 ]+~u', ' ', $str);
		$token = strtok($str, ' ');
		$array = array();
		while ($token) {
			$token = trim($token);
			if (strlen($token)>=4) {
				if (!isset($array[$token])) $array[$token]=0;
				$array[$token]++;
			}
			$token = strtok(' ');
		}
		if (count($array)>0) {
			arsort ($array);
			foreach ($array as $key=>$value) {
				if (strlen($keywords.', '.$key)>255) break;
				$keywords.= ', '.$key;
			}
			return substr($keywords, 2);
		}
	}
}

//���������� �������� �� ������
function description($str) {
	$description = '';
	$str = preg_replace("/&[\w]+;/", ' ',$str);	//������ �������� ���� &nbsp; �� ������
	$str = trim(strip_tags($str));
	$token = strtok($str, ' ');
	while ($token) {
		$token = trim($token);
		if ($token!='') {
			if (strlen($description.' '.$token)>255) break;
			$description.= trim($token).' ';
		}
		$token = strtok(' ');
	}
	return trim($description);
}

//�������������� �������� � ��������
function trunslit($str){
	$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
	$str = str_replace(
		array('a','o','u','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�'),
		array('a','o','u','a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','shch','','y','','e','yu','ya','i','yi','e'),
		$str
	);
	$str = preg_replace('~[^-a-z0-9_.]+~u', '-', $str);	//�������� ������ ��������
	$str = preg_replace('~[-]+~u','-',$str);			//�������� ������ -
	$str = trim($str,'-');								//������� �� ����� -
	$str = trim($str,'.');
	return $str;
}

//�a���� ������� strtolower
if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str,$enc = 'UTF-8') {
		$large = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','A','A','A','A','A','A','?','C','E','E','E','E','I','I','I','I','?','N','O','O','O','O','O','O','U','U','U','U','Y','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
		$small = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','a','a','a','a','a','a','?','c','e','e','e','e','i','i','i','i','?','n','o','o','o','o','o','o','u','u','u','u','y','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�');
		return str_replace($large,$small,$str);
	}
}
//��������� ��� �������������
function strtolower_utf8($str){
	return mb_strtolower($str,'UTF-8');
}

//������������� �������� �����
function time_zone($date,$time_zone = false) {
	if ($time_zone==false) {
		if (access('user auth')==false) return $date;
		global $user;
		$time_zone = $user['time_zone'];
	}
	if ($time_zone==4) return $date;
	return strftime('%Y-%m-%d %H:%M:%S',(strtotime($date)+($time_zone-4)*60*60));
}

//���������� ���� � �����
function zerofill($number,$n = 7) {
	return str_pad($number,$n,'0',STR_PAD_LEFT);
}

//����������� ����
function date2($date,$type) {
	$months = array(
		'01'	=>	'������',
		'02'	=>	'�������',
		'03'	=>	'�����',
		'04'	=>	'������',
		'05'	=>	'���',
		'06'	=>	'����',
		'07'	=>	'����',
		'08'	=>	'�������',
		'09'	=>	'��������',
		'10'	=>	'�������',
		'11'	=>	'������',
		'12'	=>	'�������',
	);
	if ($type=='d month y') {
		if (is_string($date)) $date = strtotime($date);
		$d = strftime('%d',$date);
		$m = strftime('%m',$date);
		$y = strftime('%Y',$date);
		return $d.' '.$months[$m].' '.$y;
	}
	else return strftime($type,strtotime($date));
}

//��������� ������
function about($text,$lenght = 1000,$strip_tags = '<br><img>',$end = '..') {
	$text  = strip_tags($text,$strip_tags);
	if (strlen($text)>$lenght) {
		$text = iconv_substr($text,0,$lenght,"UTF-8");
		$text.= $end;
	}
	return $text;
}
?>