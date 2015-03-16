<a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$modules['basket']?>/<?=$q['id']?>/<?=md5($q['id'].$q['date'])?>">Посмотреть заказ</a>
<?=html_array('order/mail',$q)?>