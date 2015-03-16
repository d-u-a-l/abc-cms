Для товара <a href="http://<?=$_SERVER['SERVER_NAME']?>/<?=$modules['shop']?>/<?=$q['product']['category_url']?>/<?=$q['product']['id']?>-<?=$q['product']['url']?>/"><?=$q['product']['name']?></a> добавлен <a href="http://<?=$_SERVER['SERVER_NAME']?>/admin.php?m=shop_reviews&id=<?=$q['id']?>">отзыв</a>
<br />
<?=$q['email']?>
<br /><?=$q['name']?> <?=date2($q['date'],'%d.%m.%Y')?>
<br /><?=$q['text']?>